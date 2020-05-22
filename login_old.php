<?php

	session_start();
	
	include('prepend.php');

	$_SESSION = array();
	$_SESSION['form'] = "LogForm";
	$_SESSION['agent'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['submitted'] = 'false';


?>

<script language="JavaScript">
<!--

$('.message a').click(function(){
   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
});

//-->
</script>

<center>
<H1>Login Please</H1>
</center>

<div class="login-page">
  <div class="form">
    <form class="register-form">
      <input type="text" placeholder="name"/>
      <input type="password" placeholder="password"/>
      <input type="text" placeholder="email address"/>
      <button>create</button>
      <p class="message">Already registered? <a href="#">Sign In</a></p>
    </form>
    <form class="login-form">
      <input type="text" placeholder="username"/>
      <input type="password" placeholder="password"/>
      <button>login</button>
      <p class="message">Not registered? <a href="#">Create an account</a></p>
    </form>
  </div>
</div>

<script language="JavaScript">
<!--
	document.getElementById("dob_month").focus()
//-->
</script>

<?php
  include('./append.php');
?>
