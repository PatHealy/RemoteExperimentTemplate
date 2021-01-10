<!-- This page was used to automatically send out emails at 9am every morning to users who had recently completed the study activities. HTTP requests were simply scheduled from another remote source to this domain (this-domain.com/email/send_emails.php) at 9am every morning and this script would send emails to all participants added to the 'to_send_emails' queue. -->
<?php
header("Access-Control-Allow-Origin: *");
include('../library.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function send_email($emailText, $toEmail){
	$from = 'STUDY EMAIL GOES HERE';
	$email_pw = 'EMAIL PASSWORD GOES HERE';
	$to = $toEmail;
	$subject = "Multimedia Discourse Study: Follow-Up!";
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
		return false;
	}
}

$to_send = file('to_send_emails.txt');
$already_sent = file('sent_emails.txt');
$sent_file = fopen('sent_emails.txt', "a");

$email_link = $url . "follow_up.php?email=";
$email_text_1 = "Hello,<br/><br/>Thank you for participating in our study! You should be receiving a separate email soon about receiving compensation for your participation, but we'd like to inform you of a potential opportunity regarding this compensation.<br/><br/>Our study was largely focused on the political issue of a wealth tax. For those participants who find themselves especially interested in supporting a wealth tax, we wanted to offer an opportunity for you to support political movements and candidates who are fighting for it. If you would like to take some of the $15 we will be giving you and put it towards such a cause, <a href=\"";
$email_text_2 = "\">please check out this link</a>. Clicking this link doesn't commit you to donating, it will only lead you to more information. <br/><br/>Thank you, again!<br/>Pat Healy<br/>PhD Student in Information Science";

foreach ($to_send as $line) {
	if(!in_array($line, $already_sent)){
		fwrite($sent_file, $line);
		array_push($already_sent, $line);
		$email = trim($line);
		$personalized_text = $email_text_1 . $email_link . $email . $email_text_2;
		send_email($personalized_text, $email);
	}
}

fclose($sent_file);

?>