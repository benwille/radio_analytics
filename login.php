
<?php
  include("./header.php");

    $_SESSION = array();
    $_SESSION['form'] = "LoginForm";
    $_SESSION['agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['submitted'] = 'false';
?>

<style>

#container {
    /* padding: 50; */
    width: auto;
    height: auto;
    max-width: 350px;
    background: #fff;
    border-radius: 3px;
    border: 1px solid #ccc;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
}
form {
    margin: 0 auto;
    margin-top: 20px;
}
label {
    color: #555;
    display: inline-block;
    margin-left: 18px;
    padding-top: 10px;
    font-size: 14px;
}
p a {
    color: gray;
}
p {
  margin-bottom: 0;
  padding-bottom: 1rem;
}
input {
    font-family: "Helvetica Neue", Helvetica, sans-serif;
    font-size: 12px;
    outline: none;
}
input[type=text],
input[type=password] {
    color: #777;
    padding-left: 10px;
    margin: 10px;
    margin-top: 12px;
    margin-left: 18px;
    width: 90%;
    height: 35px;
    border: 1px solid #c7d0d2;
    border-radius: 2px;
    box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .4), 0 0 0 5px #f5f7f8;
    -webkit-transition: all .4s ease;
    -moz-transition: all .4s ease;
    transition: all .4s ease;
    }
input[type=text]:hover,
input[type=password]:hover {
    border: 1px solid #b6bfc0;
    box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .7), 0 0 0 5px #f5f7f8;
}
input[type=text]:focus,
input[type=password]:focus {
    border: 1px solid #a8c9e4;
    box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .4), 0 0 0 5px #e6f2f9;
}
#lower {
    background: #ecf2f5;
    width: 100%;
    height: auto;
    margin-top: 20px;
    box-shadow: inset 0 1px 1px #fff;
    border-top: 1px solid #ccc;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
}
input[type=checkbox] {
    margin-left: 20px;
    margin-top: 30px;
}
.check {
    margin-left: 3px;
    font-size: 11px;
    color: #444;
    text-shadow: 0 1px 0 #fff;
}
input[type=submit] {
    float: right;
    margin-right: 20px;
    margin-top: 20px;
    width: 80px;
    height: 30px;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    background-color: #acd6ef; /*IE fallback*/
    background-image: -webkit-gradient(linear, left top, left bottom, from(#acd6ef), to(#6ec2e8));
    background-image: -moz-linear-gradient(top left 90deg, #acd6ef 0%, #6ec2e8 100%);
    background-image: linear-gradient(top left 90deg, #acd6ef 0%, #6ec2e8 100%);
    border-radius: 30px;
    border: 1px solid #66add6;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .3), inset 0 1px 0 rgba(255, 255, 255, .5);
    cursor: pointer;
}
input[type=submit]:hover {
    background-image: -webkit-gradient(linear, left top, left bottom, from(#b6e2ff), to(#6ec2e8));
    background-image: -moz-linear-gradient(top left 90deg, #b6e2ff 0%, #6ec2e8 100%);
    background-image: linear-gradient(top left 90deg, #b6e2ff 0%, #6ec2e8 100%);
}
input[type=submit]:active {
    background-image: -webkit-gradient(linear, left top, left bottom, from(#6ec2e8), to(#b6e2ff));
    background-image: -moz-linear-gradient(top left 90deg, #6ec2e8 0%, #b6e2ff 100%);
    background-image: linear-gradient(top left 90deg, #6ec2e8 0%, #b6e2ff 100%);
}
</style>


<script language="JavaScript">
<!--

function check(f)
{

    if (f.username.value.length <= 0)
    {
        alert("You must enter a user name");
        f.username.focus();
        return(false);
    }
    if (f.password.value.length <= 0)
    {
        alert("You must enter a password.");
        f.password.focus();
        return(false);
    }

    return(true);
}

//-->
</script>


<!-- Begin Page Content -->
<center>
<br/>
<div id="container">
    <form name="daForm" class="login" method="post" action="index.php" autocomplete="off" onSubmit="return check(this)">
        <dl><dt><label for="username">Username:</label></dt>
        <dd><input type="text" id="username" name="username" size=30 maxlength=15 tabindex="45"></dd></dl>
        <dl><dt><label for="password">Password:</label></dt>
        <dd><input type="password" id="password" name="password" size=30 maxlength=15 tabindex="55"></dd></dl>
        <div id="lower">
            <input type="checkbox"><label class="check" for="checkbox">Keep me logged in</label>
            <input type="submit" value="Login">
            <br/>
            <br/>
            <p>Forgot <a href="./forgotpassword.php">password?</a></p>
            <p>Register <a href="./register.php">here</a></p>
        </div><!--/ lower-->
        <input type="hidden" name="daform" value="LoginForm">
    </form>
</div><!--/ container-->
<br/>
</center>

<?php
  include ("footer.php")
?>
