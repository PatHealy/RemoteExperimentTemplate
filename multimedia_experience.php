<?php
	include('library.php');
	function verify_user($user_id_num, $user_password){
	  $db = new Database();
	  $db->connect();
	  if(!is_numeric($user_password)){
	    return false;
	  }
	  $int_id = $user_id_num + 0;
	  $int_pw = $user_password + 0;

	  if($db->select("participant",  "*", null, "pk_participant_id = " . $int_id)){
	    $results = $db->getResult();
	    if($results[0]["pw"] == $int_pw){
	      $stage = $results[0]["current_stage"];
	      $intervention_group = $results[0]["intervention_group"];
	      $email = $results[0]["email"];
	      if($stage == 2){
	        return array($intervention_group, $email);
	      }
	    }
	  }
	  return array(false,false);
	}
	$user_id = $_GET['u'];
	$user_pw = $_GET['p'];
	$user_data = verify_user($user_id, $user_pw);
	if($user_data[1] == false){
		header('Location: ' . $url);
	}else{
		if($user_data[0] == 1){
			//text group
			header('Location: ' . $url . "Text/index.html?email=" . $user_data[1]);
		}else if($user_data[0] == 2){
			//game group
			header('Location: ' . $url . "Game/index.php?email=" . $user_data[1]);
		}else{
			header('Location: ' . $url);
		}
	}
?>