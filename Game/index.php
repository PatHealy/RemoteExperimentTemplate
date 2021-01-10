<?php
$email = $_GET['email'];
?>
<!DOCTYPE html>
<html lang="en-us">
  <head>
    <title>Game!</title>
    <link href="../css/styles.css" rel="stylesheet" />
  </head>
  <body>
    <div class="container">
      <br/>
      <h3>For your Multimedia Experience, you'll be playing a short video game!</h3>
      <br/>
      <p>Due to a technical limitation, the game is hosted on a separate website and requires you to input your email once again.</p>
      <p>You must input the email you've used when registering for this study: <?php echo $email; ?></p>
      <p>Opening the page itself requires a password: oligarch</p>
      <br/>
      <h3>Please leave this tab open, but <a href="https://multimediadiscoursestudy.itch.io/trillionaire" target="_blank">click this link to continue to the game.</a></h3>
      <br/>
      <h4>When you finish the game and it says you may proceed to the next portion of the study, <a id="next_link">please click this link.</a></h4>
      <p>The application will stop you from proceeding through the study until you complete the game.</p>
    </div>
    <script type="text/javascript">
      var u = localStorage.getItem("user_id");
      var p = localStorage.getItem("user_pw");
      var link_text = "/?u=" + u + "&p=" + p + "#post-test";
      document.getElementById("next_link").href = link_text;
    </script>
  </body>
</html>
