<?php 
include "connection.php";
session_start();
if(isset($_POST['submit'])){
    
        // $target = "img/trainees/".basename($_FILES['img']['name']);
        $height = $_POST['height'];
        $age = $_POST['age'];
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $activity = mysqli_real_escape_string($conn, $_POST['activity']);
        $goal = mysqli_real_escape_string($conn, $_POST['goal']);
        $user_email = $_SESSION['userEmail'];
        $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
        $result = mysqli_query($conn, $select); 
        $row = mysqli_fetch_array($result);
        $user_id = $row['userId'];
        // $image = $_FILES['img']['name'];


        $sql = "UPDATE trainee SET trainerImg = ?, height = ?, age = ?, gender = ?, activity = ?, goal = ? WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("siisssi",$image, $height, $age, $gender, $activity, $goal, $user_id);
            if($stmt->execute()){
                // move_uploaded_file($_FILES['img']['tmp_name'], $target);
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
}
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
    include 'TraineeMenu.php';
?>

    <!-- Hero Section Begin -->
    <section class="hero-section">
        <div class="hs-slider owl-carousel">
            <div class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
            <div class="container">
            
           
            <div class="col-lg-12 col-md-8">
                <div class="ps-item">
                    <h3 style="font-size:40px">Personal Details</h3>
                    <?php
                        $user_email = $_SESSION['userEmail'];
                        $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
                        $result = mysqli_query($conn, $select); 
                        $row = mysqli_fetch_array($result);
                        $user_id = $row['userId'];

                        
                       $select = " SELECT * FROM trainee WHERE userId = '$user_id'  ";
                       $result = mysqli_query($conn, $select); 
                       $row = mysqli_fetch_array($result);
                    ?>
                    <ul>
                        <li style="font-size:25px;margin-bottom: 5px">Height: <?php echo $row['height'] ?></li>
                        <li style="font-size:25px;margin-bottom: 5px">Age: <?php echo $row['age'] ?></li>
                        <li style="font-size:25px;margin-bottom: 5px">Gender: <?php echo $row['gender'] ?></li>
                        <li style="font-size:25px;margin-bottom: 5px">Activity: <?php echo $row['activity'] ?></li>
                        <li style="font-size:25px;margin-bottom: 5px">Goal: <?php echo $row['goal'] ?></li>
                        <li style="font-size:25px;margin-bottom: 20px">Starting Membership: <?php echo $row['startingMembership'] ?></li>
                        <li style="font-size:35px;margin-bottom: 5px;color:orange"><b>Slide To The Side To Update</b>  </li>

                    </ul>
                    
                </div>
            </div>
            
        </div> 
            
            </div>
            <div class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-6">
                            <div class="hi-text">
                            <div class="container" style="width:800px; ">
                <div class="form-container" style="padding:5px 5px 5px 5px" >
                    <form action="" style="width:800px; " method="post">
                         <h1 style="font-size:35px;margin-bottom: 0;">Update Personal details</h1>
                        <?php
                          if(isset($err)){
                             foreach($err as $err){
                               echo '<span class="error-msg">'.$err.'</span>';
                             };
                           };
                         ?>
                            <input type="number" name="height" required placeholder="enter your height">
                            <input type="number" name="age" required placeholder="enter your age">
                             <input type="text" name="gender" required placeholder="enter your gender">
                             <input type="text" name="activity" required placeholder="enter your activity">
                             <input type="text" name="goal" required placeholder="enter your goal">
                             <!-- <input type="file" name="img" accept="image/png, image/jpg, image/jpeg" required> -->
                             <input type="submit" name="submit" value="Submit" class="form-btn">
                        </form>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- ChoseUs Section Begin -->
    <section class="choseus-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Why chose us?</span>
                        <h2>PUSH YOUR LIMITS FORWARD</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-034-stationary-bike"></span>
                        <h4>Modern equipment</h4>
                        <p>At our gym, we offer the newest and best fitness equipment on the market,
                             ensuring our members get the highest quality workout experience.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-033-juice"></span>
                        <h4>Healthy nutrition plan</h4>
                        <p>Our healthy nutrition plan is designed to fuel your body and help you achieve your fitness goals.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-002-dumbell"></span>
                        <h4>professional training plan</h4>
                        <p>Our professional training plan is tailored to guide you to peak performance and optimal results.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="cs-item">
                        <span class="flaticon-014-heart-beat"></span>
                        <h4>Unique to your needs</h4>
                        <p>Our programs are unique to your needs, providing personalized solutions for your fitness journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ChoseUs Section End -->

    <!-- Banner Section Begin -->
    <!-- <section class="banner-section set-bg" data-setbg="img/banner-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="bs-text">
                        <h2>registration now to get more deals</h2>
                        <div class="bt-tips">Where health, beauty and fitness meet.</div>
                        <a href="#" class="primary-btn  btn-normal">subscribe</a>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
    <!-- Banner Section End -->

    <!-- Pricing Section Begin -->
    <!-- <?php
        include 'pricing.php';
    ?> -->
    <!-- Pricing Section End -->

    <!-- Gallery Section Begin -->
    <div class="gallery-section">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <div class="gs-item grid-wide set-bg" data-setbg="img/gallery/gallery-1.jpg">
                <a href="img/gallery/gallery-1.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
            <div class="gs-item set-bg" data-setbg="img/gallery/gallery-2.jpg">
                <a href="img/gallery/gallery-2.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
            <div class="gs-item set-bg" data-setbg="img/gallery/gallery-3.jpg">
                <a href="img/gallery/gallery-3.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
            <div class="gs-item set-bg" data-setbg="img/gallery/gallery-4.jpg">
                <a href="img/gallery/gallery-4.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
            <div class="gs-item set-bg" data-setbg="img/gallery/gallery-5.jpg">
                <a href="img/gallery/gallery-5.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
            <div class="gs-item grid-wide set-bg" data-setbg="img/gallery/gallery-6.jpg">
                <a href="img/gallery/gallery-6.jpg" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
            </div>
        </div>
    </div>
    <!-- Gallery Section End -->

    <!-- Get In Touch Section Begin -->
    <?php 
        include 'getInTouch.php';
    ?>
    <!-- Get In Touch Section End -->

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