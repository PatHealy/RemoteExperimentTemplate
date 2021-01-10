<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Enrollment in the Multimedia Discourse Study</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
    	<br/>
    	<div class="container card">
    		<h2 class="card-header">Enrollment in the Study</h2>
    		<div class="card-body">
				<?php
					include('library.php');
					use PHPMailer\PHPMailer\PHPMailer;
					use PHPMailer\PHPMailer\Exception;
					require 'PHPMailer/src/Exception.php';
					require 'PHPMailer/src/PHPMailer.php';
					require 'PHPMailer/src/SMTP.php';

					function send_email($emailText, $toEmail, $id){
						$from = 'STUDY EMAIL GOES HERE';
						$email_pw = 'EMAIL PASSWORD GOES HERE';
						$to = $toEmail;
						$subject = "Multimedia Discourse Study: You've Registered!";
						$body = $emailText;

						$mail = new PHPMailer(true);
						try {
							$mail->isSMTP();
							$mail->Host = 'smtp.gmail.com';
							$mail->SMTPAuth = true;
							$mail->Username = $from;
							$mail->Password = $email_pw;
							$mail->SMTPSecure = 'tls';
							$mail->Port = 587;

							//Recipients
							$mail->setFrom($from, 'MultimediaDiscourse Study');
							$mail->addAddress($to);

							//Content
							$mail->isHTML(true);
							$mail->Subject = $subject;
							$mail->Body    = $body;

							$mail->send();
							return true;
						} catch (Exception $e) {
							//echo 'Message could not be sent.';
							//echo 'Mailer Error: ' . $mail->ErrorInfo;
							$db3 = new Database();
							$db3->connect();
							$db->delete("participant","pk_participant_id = " . $id);
							return false;
						}
					}

					function reCaptcha($recaptcha){
					  $secret = "RECAPTCHA SECRET KEY GOES HERE";
					  $captcha_url = "https://www.google.com/recaptcha/api/siteverify";

					  $data = array(
						'secret' => $secret,
						'response' => $recaptcha
					  );
					  $options = array(
						'http' => array (
							'method' => 'POST',
							'content' => http_build_query($data)
						)
					  );

					  $context  = stream_context_create($options);
					  $verify = file_get_contents($captcha_url, false, $context);
					  return json_decode($verify, true);
					}

					$recaptcha = $_GET['g-recaptcha-response'];
					$res = reCaptcha($recaptcha);
					if(!$res['success']){
						echo "<h2>Captcha error</h2>";
					  //header('Location: ' . $url);
					}else{
						$db = new Database();
						$db->connect();

						$submitted_email = $_GET['inputEmail1'];

						$isValidEmail = false;
						$isPittEmail = false;
						//basic protection against sql code injection, then check for @pitt.edu email
						if(filter_var($submitted_email, FILTER_VALIDATE_EMAIL) && stripos($submitted_email, "DROP") === false && stripos($submitted_email, "ALTER") === false && stripos($submitted_email, "INSERT") === false) { 
							$isValidEmail = true;
							if(stripos($submitted_email,"@pitt.edu") == strlen($submitted_email) - strlen("@pitt.edu")) {
								$isPittEmail = true;
							}
						}

						if($isValidEmail && $isPittEmail){
							//check if email is already in the system
							$isAlreadyInSystem = false;

							if($db->select("participant", "*", null, "email = '" . $submitted_email . "'")){
								$results = $db->getResult();
								//print_r($results);
								//echo "<br/>";
								if(count($results) > 0){
									$isAlreadyInSystem = true;
								}
							}

							$current_finished_users = $db->getnumberfinishedsubjects();

							if($current_finished_users >= 105){
								echo "<h3>We are not currently taking users for this study!</h3><h5>We would love to accept you as a participant for this study, but unfortunately we have reached our budget for compensating study participants.</h5><p>If you would still like to participate in this study without any compensation (we love to see that enthusiasm for research!), please contact the PI directly at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a></p>";
							} else if($isAlreadyInSystem){
								echo "<h3>It seems you've already registered before!</h3><h5>According to our records, you have already registered to begin this study. We have already sent an email to " . $submitted_email . ", which contains instructions for beginning the study.</h5><p>It may take some time for this email to be delivered, but if you have any concerns that a technical problem has occured, don't hesitate to contact the primary investigator at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a>.</p>";
							} else {
								//register
								$participant_data = array(
									"pw" => random_int((int)10000000, (int)99999999),
									"intervention_group" => random_int(1,2),
									"email" => $submitted_email
								);
								
								if($db->insert("participant", $participant_data)){
									//echo "Registration successful";
									$user_id = -1;
									$db2 = new Database();
									$db2->connect();
									if($db2->select("participant", "*", null, "email = '" . $submitted_email . "'")){
										$results_two = $db2->getResult();
										//print_r($results_two);
										$user_id = $results_two[0]["pk_participant_id"];
									}

									//send the email
									$link = "<a href=\"" . $url . "?u=" . $user_id . "&p=" . $participant_data["pw"] . "#pre-test\"> please click here!</a>";
									$emailText = "Hello! <br/><br/>You're receiving this email because you registered to participate in an online study about multimedia discourse. <br/><br/> <h3>To begin your participation in this study," . $link . "</h3> <br/>We recommend that you complete the entirity of the study's research activities in one sitting, and they must be completed on a computer, not a mobile device. It should take about 30 minutes. <br/><br/>If you did not register for this study and do not know why you received this email, please contact the primary investigator of the study at pat.healy@pitt.edu. <br/><br/>Thank you!";
									if(send_email($emailText, $submitted_email, $user_id)){
										echo "<h3>Thank you for registering!</h3><h5>We have sent an email to " . $submitted_email . ", which contains instructions for beginning the study.</h5><p>This email will be coming from pitt.multimediadiscoursestudy@gmail.com. It may take some time for this email to be delivered, but if you have any concerns that a technical problem has occured, don't hesitate to contact the primary investigator at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a>.</p>";
									} else {
										echo "<h3>There seems to have been some kind of error!</h3><h5>We attempted send an email to " . $submitted_email . ", but our email server was unsuccessful.</h5><p>Feel free to return to the previous page and attempt to re-submit your request. If the problem persists, please contact the primary investigator at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a>. Thanks!</p>";
									}
								}else {
									echo "Error in registration!";
								}
							}
						}else if($isValidEmail){
							echo "<h3>We need a pitt email!</h3><h5>You gave the email " . $submitted_email . ", which does not match the format of a pitt email.</h5><p>We can only enroll pitt students in this study and use @pitt.edu emails to verify this. If you have a pitt email that does not match the @pitt.edu format, please feel free to contact the primary investigator at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a> so that we can manually enroll you in the study.</p><a class=\"btn btn-primary nav-link js-scroll-trigger\" href=\"/#start\">Return to the previous page</a>";
						}else{
							echo "<h3>Enter a valid email!</h3><h5>You gave the email " . $submitted_email . ", which does not appear to be a valid email.</h5><p>We can only enroll pitt students in this study and use @pitt.edu emails to verify this. If you have a pitt email that does not match the @pitt.edu format, please feel free to contact the primary investigator at <a href=\"mailto:pat.healy@pitt.edu\">pat.healy@pitt.edu</a> so that we can manually enroll you in the study.</p><a class=\"btn btn-primary nav-link js-scroll-trigger\" href=\"/#start\">Return to the previous page</a>";
						}
					}
				?>

			</div>
		</div>
	</body>
</html>