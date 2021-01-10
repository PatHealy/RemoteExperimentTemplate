<?php
header("Access-Control-Allow-Origin: *");
include('library.php');

function add_continue($participant_data){
	$db = new Database();
	$db->connect();
	$db->insert("intervention_continue", $participant_data);
}

function verify_user($email){
  $db = new Database();
  $db->connect();
  
  if($db->select("participant",  "*", null, "email = '" . $email . "'")){
    $results = $db->getResult();
    if($results[0]["current_stage"] > 0){
      $stage = $results[0]["current_stage"];
      $u = $results[0]["pk_participant_id"];
      if($stage == 2){
      	$participant_data = array(
      		"fk_participant_id"=> $u
      	);
      	add_continue($participant_data);
      	return true;
      }
    }
  }
  return false;
}

$email = $_GET['email'];

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

$verification = verify_user($email);
if($verification){
	echo $email;
} else{
	echo "failure";
}
?>