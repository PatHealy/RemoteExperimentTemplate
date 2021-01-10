<?php
	include('library.php');
	$pre_test_url = "PRE-TEST SURVEY URL GOES HERE";
	$post_test_url = "POST-TEST SURVEY URL GOES HERE";

	function send_final_email($email){
		$toSendFile = fopen("email/to_send_emails.txt", "a");
		fwrite($toSendFile, $email . "\n");
		fclose($toSendFile);
	}

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
          if($stage == 3){
            send_final_email($results[0]["email"]);
          }
          return $stage;
        }
      }
      return false;
    }

	$u = $_GET['u'];
	$p = $_GET['p'];
	$stage = verify_user($u, $p);

	if($stage === false){
		header('Location: ' . $url);
	}else if($stage == 1){
		// take to pre-test
		header('Location: ' . $pre_test_url);
	}else if($stage == 3){
		//take to post-test
		header('Location: ' . $post_test_url);
	}
?>