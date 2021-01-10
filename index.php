<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Multimedia Discourse Study</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:500,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,800,800i" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <!-- reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js"></script>
    </head>
    <body id="page-top">
      <?php 
        //header('Access-Control-Allow-Origin: *');
        //header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
        include('library.php');

        function update_stage($user_id_num, $new_stage){
          $update_data = array(
            "current_stage" => $new_stage
          );
          $db = new Database();
          $db->connect();
          $db->update("participant", $update_data, "pk_participant_id = " . $user_id_num);
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
              $email = $results[0]["email"];
              if($stage == 0){
                update_stage($int_id, 1);
                $stage = 1;
              }

              return array($stage, $email);
            }
          }
          return array(false,false);
        }

        $queries = array();
        parse_str($_SERVER['QUERY_STRING'], $queries);
        $user_id = $queries['u'];
        $user_pw = $queries['p'];

        $user_data = verify_user($user_id, $user_pw);
        $participant_stage = $user_data[0];
        $email = $user_data[1];

        if($user_id == NULL || $user_pw == NULL){
          // do nothing
        } else if($participant_stage !== false){
          //echo "Was verified!";
        } else {
          //echo "Could not be verified";
        }

        if($user_id == null || $user_pw == null){
          $user_id = "null";
          $user_pw = "null";
        }

        if($email == null){
          $email = "null";
        }

        $string_stage = $participant_stage ? $participant_stage : 'false';
      ?>
      <script type="text/javascript">
        function retry_login(u,p){
          location.replace("/?u=" + u + "&p=" + p);
        }
        var stage = <?php echo $string_stage; ?>;
        var user_id = <?php echo $user_id; ?>;
        var user_pw = <?php echo $user_pw; ?>;
        var email = <?php 
          if($email == "null"){
            echo $email;
          }else{
            echo "\"" . $email . "\"";
          }
           ?>;
        if(stage == false){
          //check local storage to see if user was already registered
          if(user_id == null || user_pw == null){
            var local_user_id = localStorage.getItem("user_id");
            var local_user_pw = localStorage.getItem("user_pw");
            console.log("No login!");
            if(local_user_id != null && local_user_pw != null){
              retry_login(local_user_id, local_user_pw);
            }
          } else {
            //if verification failed on existing values
            console.log("Failed login!");
            var local_user_id = localStorage.getItem("user_id");
            var local_user_pw = localStorage.getItem("user_pw");
            if(local_user_id == user_id && local_user_pw == user_pw){
              console.log("Fudged credentials in local storage!");
              localStorage.removeItem("user_id");
              localStorage.removeItem("user_pw");
              localStorage.removeItem("email");
            }else if(local_user_id != null && local_user_pw != null){
              retry_login(local_user_id, local_user_pw);
            }
          }
        } else {
          //store user in local storage
          localStorage.setItem("user_id", user_id);
          localStorage.setItem("user_pw", user_pw);
          localStorage.setItem("email", email);
        }
      </script>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse navbar-brand js-scroll-trigger" id="navbarSupportedContent">
                <ul class="navbar-nav align-middle">
                    <?php if($email != "null"){ echo "<li><h4>Logged in as " . $email . "</h4></li>"; } ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#about">About</a></li>
                    <?php if($participant_stage == false){ echo "<div style=\"text-decoration: underline;\">";} ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#start">Start the Study</a></li>
                    <?php if($participant_stage == false){ echo "</div>";} ?>
                    <?php if($participant_stage == 1){ echo "<div style=\"text-decoration: underline;\">";} ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#pre-test">Survey #1</a></li>
                    <?php if($participant_stage == 1){ echo "</div>";} ?>
                    <?php if($participant_stage == 2){ echo "<div style=\"text-decoration: underline;\">";} ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#experience">Multimedia Experience</a></li>
                    <?php if($participant_stage == 2){ echo "</div>";} ?>
                    <?php if($participant_stage == 3){ echo "<div style=\"text-decoration: underline;\">";} ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#post-test">Survey #2</a></li>
                    <?php if($participant_stage == 3){ echo "</div>";} ?>
                    <?php if($participant_stage == 4){ echo "<div style=\"text-decoration: underline;\">";} ?>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#compensation">Compensation</a></li>
                    <?php if($participant_stage == 4){ echo "</div>";} ?>
                </ul>
            </div>
        </nav>
        <!-- Page Content-->
        <div class="container-fluid p-0">
            <!-- About-->
            <section class="resume-section" id="about">
                <div class="resume-section-content">
                    <h1 class="mb-0">
                        Multimedia Discourse
                        <span class="text-primary">Study</span>
                    </h1>
                    <div class="subheading mb-5">
                        If you have questions, please email Pat Healy, the primary investigator of this study. 
                        <a href="mailto:pat.healy@pitt.edu">pat.healy@pitt.edu</a>
                    </div>
                    <p class="lead mb-5">University of Pittsburgh Undergraduate students can use this web application to participate in a short study and receive $15 in compensation. The study requires participants complete a short survey, a short Multimedia experience, and a second short survey. The content of these activities are concerned with multimedia political communication. You will receive more details about the research activites before they begin.</p>
                    <p class="lead mb-5">All activites are completed through a web browser (Google Chrome or Firefox are recommended). The study should take about 30 minutes to complete, though we encourage you to complete activities at your own pace as there is no time limit.</p>
                    <h3>Participation must be done on a computer, not a mobile device.</h3>
                    <p>Minimum Recommended Specs for Participation:</p>
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Windows</th>
                          <th scope="col">Mac</th>
                          <th scope="col">Linux</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>OS: Windows 7 / Vista / XP<br/>Processor: 3.0 GHz P4, Dual Core 2.0 (or higher) or AMD64X2 (or higher)<br/>Memory: 2 GB RAM<br/>Graphics: Video card must be 128 MB or more and with support for Pixel Shader 2.0b (ATI Radeon X800 or higher / NVIDIA GeForce 7600 or higher / Intel HD Graphics 2000 or higher).<br/>DirectX: Version 9.0c<br/>Storage: 500 MB available space</td>
                          <td>OS: MAC OS X 10.6.7 or higher<br/>Processor: Intel Core Duo Processor (2GHz or better)<br/>Memory: 2 GB RAM<br/>Graphics: ATI Radeon 2400 or higher / NVIDIA 8600M or higher / Intel HD Graphics 3000<br/>Storage: 500 MB available space</td>
                          <td>OS: Ubuntu 12.04<br/>Processor: Dual core from Intel or AMD at 2.8 GHz<br/>Memory: 2 GB RAM<br/>Graphics: nVidia GeForce 8600/9600GT, ATI/AMD Radeon HD2600/3600 (Graphic Drivers: nVidia 310, AMD 12.11), OpenGL 2.1<br/>Storage: 500 MB available space</td>
                        </tr>
                      </tbody>
                    </table>
                    <p>If your computer can run a graphically simple game, like Minecraft, you'll probably be fine!</p>
                    <a class="btn btn-primary nav-link js-scroll-trigger" href="#start">Start the study</a>
                </div>
            </section>
            <hr class="m-0" />
            <!-- Start the Study -->
            <section class="resume-section" id="start">
                <div class="resume-section-content">
                    <h2 class="mb-5">Start the Study</h2>
                    <div id="startStudyInitial">
                      <?php
                        if($participant_stage != false){
                          echo "<h3>You've already registered for this study!</h3><p>Continue on to the later steps</p>";
                        }else{
                          echo "<p>To start this study, we first must verify that you are a pitt student. Put your pitt email in the field below and we'll send you an email, which will give you a link to start the study.</p><form action=\"enroll.php\" method=\"get\"><div class=\"form-group\"><div class=\"g-recaptcha brochure__form__captcha\" data-sitekey=\"CAPTCHA KEY GOES HERE\"></div><label for=\"inputEmail1\">We require a Pitt email address and prefer your original, non-alias email (example: pwh5@pitt.edu)</label><input type=\"email\" class=\"form-control\" id=\"inputEmail1\" name=\"inputEmail1\" aria-describedby=\"emailHelp\" placeholder=\"example@pitt.edu\"><small id=\"emailHelp\" class=\"form-text text-muted\">We will never share your email with anyone. We need it only for verification and continued communication about participation (and compensation for that participation) in this study.</small></div><button type=\"submit\" class=\"btn btn-primary\">Submit</button></form>";
                        }
                      ?>
                    </div>
                    <div id="startStudyCompleted" class="hidden">
                        <p>You have already completed this portion of the study! <a class="nav-link js-scroll-trigger" href="#pre-test">Continue on to the next section!</a></p>
                    </div>
                </div>
            </section>
            <hr class="m-0" />
            <!-- Pre-Test Survey -->
            <section class="resume-section" id="pre-test">
                <div class="resume-section-content">
                    <h2 class="mb-5">Survey #1</h2>
                    <p>At this point in the study, you must complete a short survey, prefaced by a more thorough explanation of all research activities that you will participate in throughout the study.</p>
                    <p>(Estimate: about 10 minutes to complete)</p>
                    <?php
                      if($participant_stage == 1){
                        echo "<form action=\"start_survey.php\" method=\"get\"><input name=\"u\" type=\"number\" value=" . $user_id . " style=\"display:none;\"><input name=\"p\" type=\"number\" value=" . $user_pw . " style=\"display:none;\"><button class=\"btn btn-primary\">Take the Survey</button></form>";
                      }else if($participant_stage == 0 || $participant_stage === false){
                        echo "<h3>This option will only appear after you've registered and verified your email.</h3>";
                      } else {
                        echo "<h3>Our system says you've already completed survey #1. Please contact pat.healy@pitt.edu if you somehow havent.</h3>";
                      }
                    ?>
                </div>
            </section>
            <hr class="m-0" />
            <!-- Multimedia Experience -->
            <section class="resume-section" id="experience">
                <div class="resume-section-content">
                    <h2 class="mb-5">Multimedia Experience</h2>
                    <p>You must complete a short multimedia experience. This absolutely must be done on a computer and not a mobile device.</p>
                    <p>(Estimate: about 15 minutes to complete)</p>
                    <?php
                      if($participant_stage == 2){
                        echo "<form action=\"multimedia_experience.php\" method=\"get\"><input name=\"u\" type=\"number\" value=" . $user_id . " style=\"display:none;\"><input name=\"p\" type=\"number\" value=" . $user_pw . " style=\"display:none;\"><button class=\"btn btn-primary\">Complete the Multimedia Experience</button></form>";
                      }else if($participant_stage < 2 || $participant_stage === false){
                        echo "<h3>This option will only appear after you've registered, verified your email, and completed the pre-test.</h3>";
                      }else{
                        echo "<h3>Our system says you've already completed the multimedia experience. Please contact pat.healy@pitt.edu if you somehow havent.</h3>";
                      }
                    ?>
                </div>
            </section>
            <hr class="m-0" />
            <!-- Post-test Survey -->
            <section class="resume-section" id="post-test">
                <div class="resume-section-content">
                    <h2 class="mb-5">Survey #2</h2>
                    <p>The final research activitiy of the study is another short survey.</p>
                    <p>(Estimate: about 5 minutes to complete)</p>
                    <?php
                      if($participant_stage == 3){
                        echo "<form action=\"start_survey.php\" method=\"get\"><input name=\"u\" type=\"number\" value=" . $user_id . " style=\"display:none;\"><input name=\"p\" type=\"number\" value=" . $user_pw . " style=\"display:none;\"><button class=\"btn btn-primary\">Take the Second Survey!</button></form>";
                      }else if($participant_stage < 3 || $participant_stage === false){
                        echo "<h3>This option will only appear after you've registered, verified your email, completed the pre-test, and completed the multimedia experience.</h3>";
                      }else{
                        echo "<h3>Our system says you've already completed Survey #2. Please contact pat.healy@pitt.edu if you somehow havent.</h3>";
                      }
                    ?>
                </div>
            </section>
            <hr class="m-0" />
            <!-- Compensation -->
            <section class="resume-section" id="compensation">
                <div class="resume-section-content">
                    <h2 class="mb-5">Compensation</h2>
                    <p>Participants will be compensated $15 for participation in this study, only after all research activities are completed. Participants will be contacted about this over email. </p>
                    <p>Compensation is limited to one payment per person. Participants will not be paid multiple times if they manage to abuse this application and submit multiple emails. Compensation is managed by a human being and not automatically, like all other parts of this study, so if you experience any delays or otherwise have questions about compensation, please email the primary investigator at <a href="mailto:pat.healy@pitt.edu">pat.healy@pitt.edu</a></p>
                    <?php
                      if($participant_stage == 4){
                        echo "<h3>Thank you for completing all of the research activities! You will be contacted through email about compensation shortly.</h3><h4>During Survey #2, you read a petition. To clarify, the petition you've just read is not a real petition and was simply a research exercise for the purposes of this study.</h4>";
                      }
                    ?>
                </div>
            </section>
            <hr class="m-0" />
        </div>
        <!-- Bootstrap core JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <!-- Third party plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
    </body>
</html>