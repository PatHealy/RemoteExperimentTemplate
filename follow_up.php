<?php
include('library.php');

function add_log($participant_data){
	$db = new Database();
	$db->connect();
	$db->insert("clicked_followup_link", $participant_data);
}

function verify_user($email){
  $db = new Database();
  $db->connect();
  
  if($db->select("participant",  "*", null, "email = '" . $email . "'")){
    $results = $db->getResult();
    if($results[0]["current_stage"] > 0){
		$stage = $results[0]["current_stage"];
		$u = $results[0]["pk_participant_id"];
	  	$participant_data = array(
	  		"fk_participant_id"=> $u
	  	);
	  	add_log($participant_data);
	  	return true;
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
?>
<!DOCTYPE html>
<html>
<head>
	<title>Multimedia Discourse Study: Follow-up</title>
	<link href="css/styles.css" rel="stylesheet" />
</head>
<body>
	<div class="container">
		<h3>Thank you for indicating you are interested in possibly donating to a cause related to wealth taxes!</h3> 
		<p>Your interest has been recorded as part of the study. We will not track whether you choose to donate to any specific causes listed on this page, only that you have viewed this page at all.</p>
		<p>If you have any questions or concerns about this, please feel free to contact the PI, Pat Healy, at pat.healy@pitt.edu</p>
		<br/>
		<p>So, on the topic of wealth taxes...</p>
		<p>Despite somewhat widespread support for wealth taxes, it's somewhat rare for them to be explicitly endorsed by elected officials or candidates.</p>
		<p>At a national level, the loudest proponents of a wealth tax have obviously been <a href="https://elizabethwarren.com/">Elizabeth Warren</a> and <a href="https://berniesanders.com/">Bernie Sanders</a>. Though it may not seem very necessary to donate to them right now, after their huge election campaigns in the democratic primary, you still can if you want!</p>
		<p>Probably more urgently, there are some important elections coming up in November with a bunch of candidates you may want to support.</p>
		<ul>
			<li>Though neither first-party Presidential Candidate explicitly supports a wealth tax, <a href="https://joebiden.com/">Joe Biden's</a> platform includes raising the income tax rate on the top 1% of earners.</li>
			<li>More locally, <a href="https://www.summerforpa.com/">Summer Lee</a> explicitly supports a progressive income tax (i.e. The "Fair Share Tax") as a core part of her platform.</li>
			<li><a href="https://www.saraforpa.com/">Sara Innamorato</a>, State Representative for District 21, has <a href="https://www.post-gazette.com/business/money/2019/07/09/Schenley-Plaza-Tax-the-Rich-Bus-Tour-Innamorato-Tax-Cuts-Jobs-Act-Trump-Administration-GOP/stories/201907090103">expressed support for raising taxes on the wealthy</a> and more generally supports expanding unions to empower the working class.</li>
			<li><a href="https://benhamforpa.com/">Jessica Benham</a>, a candidate for State Representative in PA House District 36, is similarly supportive of empowering unions as a path to improving the rights of the working class. She has not yet stated opinions on wealth taxes publicly, but is likely worth following.</li>
		</ul>
		<p>Right on campus, you'll likely be able to find folks who support wealth taxes or otherwise support empowering the working class in the student group, <a href="https://www.facebook.com/PittCSAW/">Community & Students for All Workers (CSAW)</a>.</p>
	</div>
</body>
</html>