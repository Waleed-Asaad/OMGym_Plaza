<?php 
include "connection.php";
session_start();
if (isset($_POST['submit'])) {
    $target = "img/team/" . basename($_FILES['image']['name']);
    $name = $_POST['name'];
    $muscle_building = isset($_POST['muscle_building']) ? 1 : 0;
    $weight_loss = isset($_POST['weight_loss']) ? 1 : 0;
    $strength = isset($_POST['strength']) ? 1 : 0;
    $endurance = isset($_POST['endurance']) ? 1 : 0;
    $flexibility = isset($_POST['flexibility']) ? 1 : 0;
    $body_building = isset($_POST['body_building']) ? 1 : 0;
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
    $result = mysqli_query($conn, $select);
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];
    $image = $_FILES['image']['name'];

    $sql = "UPDATE trainer SET trainerImg = ?, trainerName = ?, muscle_building = ?, weight_loss = ?, strength = ?, flexibility = ?, endurance = ?, body_building = ? WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssiiiiiii", $image, $name, $muscle_building, $weight_loss, $strength, $flexibility, $endurance, $body_building, $user_id);
        if ($stmt->execute()) {
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    header("Location: trainer_home.php");
    exit;
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
        .star-rating1 {
            position: relative;
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin: 0 -0.25rem;
        }

        .star-input {
            position: absolute;
            opacity: 0;
        }

        .star-label {
            cursor: pointer;
            color: grey;
            padding: 0 0.25rem;
            transition: color 0.15s;
        }

        .star-input:checked ~ label {
            color: gold;
        }

        .star-input:hover ~ label {
            color: goldenrod;
            transition: none;
        }

        .star-label:active {
            color: darkgoldenrod !important;
        }

        .star-input:focus-visible + label {
            outline-offset: 1px;
            outline: #4f46e5 solid 2px;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            font-size: 1.5rem;
            justify-content: center;
            padding: 0.3rem;
        }
        .star-rating span {
            cursor: default;
            color: grey;
            padding: 0 0.1rem;
        }
        .star-rating .checked {
            color: gold;
        }
        
    </style>
</head>

<body>
<?php include 'Trainer_menu.php'; ?>

    <!-- Hero Section Begin -->
    <section class="hero-section">
        <div class="hs-slider owl-carousel">
            <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">

            <?php
                                $user_email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $select = "SELECT * FROM trainer WHERE userId = '$user_id'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                
                                $traineeImg = $row['trainerImg'];
                                echo '
                                <div style="width:400px;height:500px; margin-left:100px; margin-top:80px ;margin-right:auto;" class="gs-item grid-wide set-bg" data-setbg="img/team/'.$traineeImg.'"></div>
                                ';
                ?>
                <div class="container">
                    <div class="col-lg-10 col-md-8">
                        <div class="ps-item">
                            <h3 style="font-size:40px">Personal Details</h3>
                            <?php
                                $user_email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $select = "SELECT * FROM trainer WHERE userId = '$user_id'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $name = $row['trainerName'];
                                $muscle_building = $row['muscle_building'];
                                $weight_loss = $row['weight_loss'];
                                $strength = $row['strength'];
                                $endurance = $row['endurance'];
                                $body_building = $row['body_building'];
                                $flexibility = $row['flexibility'];
                                $cancel = $row['cancel'];
                                $rating = $row['rating'];
                                $trainerImg = $row['trainerImg'];
                                echo '
                                <ul>
                                    
                                    <div class="specialty">
                                    <li style="font-size:25px;margin-bottom: 20px">Name: ' . htmlspecialchars($name) . '</li>
                                    <li style="font-size:25px;margin-bottom: 5px">Speciality:</li>';
                                    if ($muscle_building) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Muscle Building</li>';
                                    }
                                    if ($weight_loss) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Weight Loss</li>';
                                        
                                    }
                                    if ($strength) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Strength</li>';
                                        
                                    }
                                    if ($endurance) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Endurance</li>';
                                        
                                    }
                                    if ($body_building) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Bodybuilding</li>';
                                        
                                    }
                                    if ($flexibility) {
                                        echo '<li style="font-size:25px;margin-bottom: 5px">Flexibility</li>';
                                        
                                    }
                                    echo '</div>
                                    <li style="font-size:25px;margin-bottom: 20px">Cancels: ' . htmlspecialchars($cancel) . '</li>
                                    <div  class="star-rating">';
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($i <= round($rating)) ? "checked" : "";
                                        echo '<span  class="fa fa-star '.$checked.'"></span>';
                                    }
                                     echo '  </div>
                                    <li style="font-size:35px;margin-bottom: 5px;color:orange"><b>Slide To The Side To Update</b></li>
                                </ul>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-4">
                            <div class="hi-text">
                                <div class="container" style="width:800px;">
                                    <div class="form-container" style="padding:5px 5px 5px 5px">
                                        <form action="" method="post" enctype="multipart/form-data" style="width:800px;">
                                            <h1 style="font-size:35px;margin-bottom: 0;">Update Personal details</h1>
                                            <?php if (isset($err)) {
                                                foreach ($err as $err) {
                                                    echo '<span class="error-msg">' . htmlspecialchars($err) . '</span>';
                                                }
                                            } ?>
                                            <?php
                                $user_email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $select = "SELECT * FROM trainee WHERE userId = '$user_id'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                
                                $name = $row['trainerName'];
                                $name_placeholder = isset($height) ? $height : 'Enter your name';
                                echo '
                                            <input type="text" name="name" required placeholder="'.$name_placeholder.'">
                                            <div class="specialty">
                                                <label style="color:white">Specialty:</label><br>
                                                <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                                <input type="checkbox" id="muscle_building" name="muscle_building" value="1"><br>
                                                <label for="weight_loss" style="color:white">Weight Loss</label><br>
                                                <input type="checkbox" id="weight_loss" name="weight_loss" value="1"><br>
                                                <label for="strength" style="color:white">Strength</label><br>
                                                <input type="checkbox" id="strength" name="strength" value="1"><br>
                                                <label for="endurance" style="color:white">Endurance</label><br>
                                                <input type="checkbox" id="endurance" name="endurance" value="1"><br>
                                                <label for="flexibility" style="color:white">Flexibility</label><br>
                                                <input type="checkbox" id="flexibility" name="flexibility" value="1"><br>
                                                <label for="body_building" style="color:white">Body Building</label><br>
                                                <input type="checkbox" id="body_building" name="body_building" value="1"><br>
                                            </div>
                                            <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" required>
                                            <input type="submit" name="submit" value="Submit" class="form-btn">
                                        ';
                                        ?>
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
                        <p>At our gym, we offer the newest and best fitness equipment on the market, ensuring our members get the highest quality workout experience.</p>
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
                        <h4>Professional training plan</h4>
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

    <!-- Get In Touch Section Begin -->
    <?php include 'getInTouch.php'; ?>
    <!-- Get In Touch Section End -->

    <!-- Footer Section Begin -->
    <?php include 'footer.php'; ?>
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