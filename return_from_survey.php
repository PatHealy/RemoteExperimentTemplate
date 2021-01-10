<?php
include('library.php');

function update_stage($user_id_num, $new_stage){
	$update_data = array(
		"current_stage" => $new_stage
	);
	$db = new Database();
	$db->connect();
	$db->update("participant", $update_data, "pk_participant_id = " . $user_id_num);
}

function verify_user($email, $current_stage){
  $db = new Database();
  $db->connect();
  
  if($db->select("participant",  "*", null, "email = '" . $email . "'")){
    $results = $db->getResult();
    if($results[0]["current_stage"] > 0){
      $stage = $results[0]["current_stage"];
      $u = $results[0]["pk_participant_id"];
      $p = $results[0]["pw"];
      if(($stage == 1 && $current_stage == 1) || ($stage == 3 && $current_stage == 3)){
      	update_stage($u, $current_stage + 1);
      	return array($u, $p, $current_stage + 1);
      }
    }
  }
  return array(false, false, false);
}

$code1 = 28914356;
$code2 = 89458934;

$given_code = $_GET['c'];
$email = $_GET['email'];
$current_stage = 0;

$isValidEmail = false;
$isPittEmail = false;
if(filter_var($email, FILTER_VALIDATE_EMAIL) && stripos($email, "DROP") === false && stripos($email, "ALTER") === false && stripos($email, "INSERT") === false) { 
	$isValidEmail = true;
	if(stripos($email,"@pitt.edu") == strlen($email) - strlen("@pitt.edu")) {
		$isPittEmail = true;
	}
}

if(!$isValidEmail || !$isPittEmail){
	$email = "";
}

if($code1 == $given_code){
	$current_stage = 1;
} else if ($code2 == $given_code){
	$current_stage = 3;
} else{
	header('Location: '. $url);
}

$user_data = verify_user($email, $current_stage);
$u = $user_data[0];
$p = $user_data[1];
$current_stage = $user_data[2];

if($current_stage == 2){
	header('Location: ' . $url . "?u=" . $u . "&p=" . $p . "#experience");
}else if($current_stage == 4){
	header('Location: ' . $url . "?u=" . $u . "&p=" . $p . "#compensation");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript">
	var email = localStorage.getItem("email");
	var url = window.location.href;
	if(!url.includes("&email")){
		if(email != null){
			url = url + "&email=" + email;
			window.location.replace(url);
		}
	}

	function load_email(){
		email = document.getElementById("inputEmail1").value;
		localStorage.setItem("email",email);
		if(email != null){
			if(!url.includes("&email")){
				url = url + "&email=" + email;
			}else{
				url = url.substring(0, url.indexOf("&email=")) + "&email=" + email;
			}
			window.location.replace(url);
		}
	}

	</script>
	<link href="css/styles.css" rel="stylesheet" />
</head>
<body>
	<div class="container">
		<br/>
		<h3>Something seems to have gone wrong!</h3>
		<p>The purpose of this page is to verify that you have completed one of the surveys. This will only work automatically if you are using the same browser as when you initially enrolled in the survey.</p>
		<div class="form-group">
			<label for="inputEmail1">To continue, please provide your pitt email (the same email you used to enroll in this study) in this box:</label>
			<input type="email" class="form-control" id="inputEmail1" name="inputEmail1" aria-describedby="emailHelp" placeholder="example@pitt.edu">
		</div>
		<button class="btn btn-primary" onclick="load_email()">Submit</button>
		<br/><br/>
		<p>The error will persist if you enter the wrong email in the above box. Contact the primary investigator (pat.healy@pitt.edu) if you need help.</p>
	</div>
</body>
</html>