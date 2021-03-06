# RemoteExperimentTemplate
A php application that may form a template for running simple asynchronous human-subjects experiments remotely.

### This is the source code for a web application used to run my 'Multimedia Discourse' Study completely remotely and asynchronously in the Fall of 2020, in the midst of the COVID-19 global pandemic. If you have some understanding of php, you should be able to modify this existing source code to run your own similar human-subjects study, assuming it is similar in format to mine.

In my study, subjects first gave their email and passed a ReCaptcha. If the email was in the appropriate format (in this case, I required their email end in "@pitt.edu", because I was only recruiting University of Pittsburgh students), the application would automatically send an email to this email with a unique link for them to click to verify. This link brought them back to the web application, passing a user id and randomly generated passcode through a query string to authenticate, which is then stored in browser local storage for the rest of the experiment.

The rest of the application's operations use a similar method of verification (through user ids, emails, and randomly generated passcodes stored in browser local storage) to bring the user to a pre-test survey (hosted elsewhere, such as Qualtrics), randomly assign them to one of two interventions, present them a post-test survey, and send them a follow-up email the day after they had completed the research activities. 

### So, basically, if you're looking to run an experiment with two randomly assigned treatments and a pre and post-test survey, this may be a useful template for you to work off of. My code is unfortunately only lightly commented at this point (and the files themselves are not very well-organized), but I hope to update that in the near future. For now, feel free to leave me issues and I'll do my best to respond at my earliest convenience.
