<?php 
include "connection.php";
session_start();
if(isset($_POST['submit'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
 
    $select = " SELECT * FROM user WHERE userEmail = '$email' && userPassword = '$pass' ";
 
    $result = mysqli_query($conn, $select);
 
    if(mysqli_num_rows($result) > 0){
 
       $row = mysqli_fetch_array($result);
        $_SESSION['userEmail'] = $row['userEmail'];
        $_SESSION['userName'] = $row['userName'];
       echo '<script type="text/javascript">
        if("' . $row['status'] . '"=="trainee"){
            window.location = "traineeHome.php";
          }
        else if("' . $row['status'] . '"=="user")
         window.location = "user-home.php";
        else{
        window.location = "trainer_home.php";
        }
       </script>';
      
    }else{
       $error[] = 'incorrect email or password!';
    }
 };
 if(isset($_POST['submit1'])){
    // $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
 
    $select = " SELECT * FROM user WHERE userEmail = '$email' && userPassword = '$pass' ";
 
    $result = mysqli_query($conn, $select);
 
    if(mysqli_num_rows($result) > 0){
 
       $err[] = 'user already exist!';
 
    }
    else{
 
       if($pass != $cpass){
          $err[] = 'password not matched!';
       }else{
          $insert = "INSERT INTO user(userName,userAddress, userEmail, userPassword,status) VALUES('$name','$address','$email','$pass','user')";
          mysqli_query($conn, $insert);
          header('location:user-home.php');
       }
    }
 
 };
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Template">
    <meta name="keywords" content="Gym, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gym | Template</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    
</head>

<body>
<?php
    include 'menu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>LOGIN</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <a href="#">Pages</a>
                            <span>LOGIN</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

<style><?php 
        include "C:\wamp64\www\omgym_plaza\css\style.css";
    ?></style>    
<div class="loginORregister"> 
<div class="form-container">
   <form action="" method="post">
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
	  <p>are you admin? <a href="admin-login.php">click here</a></p>
   </form>
</div>
<div class="form-container">
    <form action="" method="post">
        <h1>Register now</h1>
        <?php
      if(isset($err)){
         foreach($err as $err){
            echo '<span class="error-msg">'.$err.'</span>';
         };
      };
      ?>
        <!-- <input type="number" name="id" required placeholder="enter your ID"> -->
        <input type="text" name="name" required placeholder="enter your name">
        <input type="text" name="address" required placeholder="enter your address">
        <input type="email" name="email" required placeholder="enter your email">
        <input type="password" name="password" required placeholder="enter your password">
        <input type="password" name="cpassword" required placeholder="confirm your password">
        <input type="submit" name="submit1" value="register now" class="form-btn">
   </form>
</div>
</div>
    
    

    <!-- Footer Section Begin -->
    <?php 
        include 'footer.php';
    ?>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>
    <script src="js/jquery.barfiller.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>



</body>

</html>