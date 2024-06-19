<?php

include 'connection.php';
session_start();
if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = $_POST['password'];

   $select = " SELECT * FROM user WHERE userEmail = '$email' && userPassword = '$pass' ";

   $result = mysqli_query($conn, $select);

   // if(mysqli_num_rows($result) > 0){

   //    $row = mysqli_fetch_array($result);
	//    $_SESSION['userId'] = $row['userId'];
	//    $_SESSION['userName'] = $row['userName'];
   //    // echo '<script type="text/javascript">
   //    //   window.location = "user-home.php";
   //    // </script>';
     
   // }else{
   //    $error[] = 'incorrect email or password!';
   // }
};
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <style>
    <?php 
        include "C:\wamp64\www\omgym_plaza\css\style.css";
    ?>
    </style>
    </head>
<body>
<div class="loginORregister"> 
<div class="form-container">
   <form action="" method="POST">
      <h1>Login now</h1>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="email" name="email" required placeholder="enter your email">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="submit" name="submit" value="login now" class="form-btn">
      <p>don't have an account? Register now <span>&#8594;</span></p>
	  <p>are you admin? <a href="admin_login.php">click here</a></p>
   </form>
</div>
<div class="form-container">
    <form action="" method="POST">
        <h1>Register now</h1>
        <input type="text" name="name" required placeholder="enter your name">
        <input type="email" name="email" required placeholder="enter your email">
        <input type="password" name="password" required placeholder="enter your password">
        <input type="password" name="cpassword" required placeholder="confirm your password">
        <input type="submit" name="submit" value="register now" class="form-btn">
   </form>
</div>
</div> 
</body>
</html>