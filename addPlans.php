<?php 
include "connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $select);
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];

    echo '<h1>worked</h1>';

    $sql = "SELECT * FROM trainer WHERE userId = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $trainer_id = $row['trainerId'];

    // Handle Training Plan submission
    if (isset($_POST['submit1'])) {
        $target = "img/training_plans/" . basename($_FILES['image1']['name']);
        $image = $_FILES['image1']['name'];

        if ($_FILES['image1']['error'] === 0) {
            $muscle_building = isset($_POST['muscle_building1']) ? 1 : 0;
            $fat_loss = isset($_POST['fat_loss1']) ? 1 : 0;
            $strength = isset($_POST['strength1']) ? 1 : 0;
            $endurance = isset($_POST['endurance1']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility1']) ? 1 : 0;
            $body_building = isset($_POST['body_building1']) ? 1 : 0;

            $sql = "INSERT INTO training_plan (planImage, trainerId, muscle building, fat loss, strength, endurance, flexibility, body building) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("siiiiiii", $image, $trainer_id, $muscle_building, $fat_loss, $strength, $endurance, $flexibility, $body_building);
                if ($stmt->execute()) {
                    move_uploaded_file($_FILES['image1']['tmp_name'], $target);
                    echo "Record updated successfully";
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error uploading file: " . $_FILES['image1']['error'];
        }
        header("Location:addPlans.php");
        exit;
    }

    // Handle Meal Plan submission
    if (isset($_POST['submit2'])) {
        $target = "img/meal_plans/" . basename($_FILES['image2']['name']);
        $image = $_FILES['image2']['name'];

        if ($_FILES['image2']['error'] === 0) {
            $muscle_building = isset($_POST['muscle_building2']) ? 1 : 0;
            $fat_loss = isset($_POST['fat_loss2']) ? 1 : 0;
            $strength = isset($_POST['strength2']) ? 1 : 0;
            $endurance = isset($_POST['endurance2']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility2']) ? 1 : 0;
            $body_building = isset($_POST['body_building2']) ? 1 : 0;

            $sql = "INSERT INTO meal_plans (planImage, trainerId, muscle building, fat loss, strength, endurance, flexibility, body building) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("siiiiiii", $image, $trainer_id, $muscle_building, $fat_loss, $strength, $endurance, $flexibility, $body_building);
                if ($stmt->execute()) {
                    move_uploaded_file($_FILES['image2']['tmp_name'], $target);
                    echo "Record updated successfully";
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error uploading file: " . $_FILES['image2']['error'];
        }
        header("Location:addPlans.php");
        exit;
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

    <style>
        #image1{
            background-color:black;
            color:white;
        }

        #image2{
            background-color:black;
            color:white;
        }
    </style>
</head>

<body>
<?php
    include 'Trainer_menu.php';
?>

    <!-- Hero Section Begin -->
    <section class="hero-section">
        <div class="hs-slider owl-carousel">
            <div class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
            <div class="container">
            
           
            <div class="row">
                        <div class="col-lg-6 offset-lg-4">
                            <div class="hi-text">
                            <div class="container" style="width:800px">
                <div class="form-container" style="padding:5px 5px 5px 5px" >
                    <form action="" method="post" enctype="multipart/form-data" style="width:800px; ">
                         <h1 style="font-size:35px;margin-bottom: 0;">Add Training Plan</h1>
                        <?php
                          if(isset($err)){
                             foreach($err as $err){
                               echo '<span class="error-msg">'.$err.'</span>';
                             };
                           };
                         ?>
                            
                             <input type="file" id="image1" name="image1" accept="image/*" required >
                             <div class="specialty">
                                <label style="color:white">Specialty:</label><br>
                                <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                <input type="checkbox" id="muscle_building1" name="muscle_building1" value="muscle_building">
                                <label for="fat_loss" style="color:white">Fat Loss</label><br>
                                <input type="checkbox" id="fat_loss1" name="fat_loss1" value="fat_loss">
                                <label for="strength" style="color:white">Strength</label><br>
                                <input type="checkbox" id="strength1" name="strength1" value="strength">
                                <label for="endurance" style="color:white">Endurance</label><br>
                                <input type="checkbox" id="endurance1" name="endurance1" value="endurance">
                                <label for="flexibility" style="color:white">Flexibility</label><br>
                                <input type="checkbox" id="flexibility1" name="flexibility1" value="flexibility">
                                <label for="body_building" style="color:white">Body Building</label><br>
                                <input type="checkbox" id="body_building1" name="body_building1" value="body_building">
            
                            </div>
                             <input type="submit" name="submit1" value="Submit" class="form-btn" >
                        </form>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
            
        </div> 
            
            </div>
            <div class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-4">
                            <div class="hi-text">
                            <div class="container" style="width:800px">
                <div class="form-container" style="padding:5px 5px 5px 5px" >
                    <form action="" method="post" enctype="multipart/form-data" style="width:800px; ">
                         <h1 style="font-size:35px;margin-bottom: 0;">Add Meal Plan</h1>
                        <?php
                          if(isset($err)){
                             foreach($err as $err){
                               echo '<span class="error-msg">'.$err.'</span>';
                             };
                           };
                         ?>
                            <input type="file" id="image2" name="image2" accept="image/*" required >
                            <div class="specialty">
                                <label style="color:white">Specialty:</label><br>
                                <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                <input type="checkbox" id="muscle_building" name="muscle_building2" value="muscle_building">
                                <label for="fat_loss" style="color:white">Fat Loss</label><br>
                                <input type="checkbox" id="fat_loss" name="fat_loss2" value="fat_loss">
                                <label for="strength" style="color:white">Strength</label><br>
                                <input type="checkbox" id="strength" name="strength2" value="strength">
                                <label for="endurance" style="color:white">Endurance</label><br>
                                <input type="checkbox" id="endurance" name="endurance2" value="endurance">
                                <label for="flexibility" style="color:white">Flexibility</label><br>
                                <input type="checkbox" id="flexibility" name="flexibility2" value="flexibility">
                                <label for="body_building" style="color:white">Body Building</label><br>
                                <input type="checkbox" id="body_building" name="body_building2" value="body_building">
            
                            </div>
                             <input type="submit" name="submit2" value="Submit" class="form-btn">
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