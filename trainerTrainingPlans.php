<?php 
include "connection.php";
session_start();

$user_email = $_SESSION['userEmail'];
$select = "SELECT * FROM user WHERE userEmail = '$user_email'";
$result = mysqli_query($conn, $select);
$row = mysqli_fetch_array($result);
$user_id = $row['userId'];

$select = "SELECT * FROM trainer WHERE userId = '$user_id'";
$result = mysqli_query($conn, $select);
$row = mysqli_fetch_array($result);
$trainerId = $row['trainerId'];



// Deleting a training plan
if (isset($_POST['delete'])) {
    $training_planId = $_POST['delete'];
    $deleteSql = "DELETE FROM training_plan WHERE training_planId = $training_planId";
    mysqli_query($conn, $deleteSql);
    header("Location: trainerTrainingPlans.php");
    exit; // Prevent further code execution
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
<?php include 'trainer_menu.php'; ?>
<section class="breadcrumb-section set-bg" data-setbg="img/hero/hero-2.jpg" >
        <div class="container">
        </div>
        </section>
    <!-- Gallery Section Begin -->
     
    <div class="gallery-section" style="height:2000px;">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <?php
            $sql = "SELECT * FROM training_plan WHERE trainerId = '$trainerId'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $planImage = $row['planImage'];
                    echo '<script>console.log('.$planImage.');</script>';
                    echo "<div class='gs-item grid-wide set-bg' data-setbg='img/training_plans/$planImage' style='width:350px; height:300px;margin-bottom:50px; margin-right:15px'>";
                    echo "<a href='img/training_plans/$planImage' class='thumb-icon image-popup'><i class='fa fa-picture-o'></i></a>";
                    echo "<div class='product-actions'>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='delete' value='{$row["training_planId"]}'>";
                    echo "<button type='submit' style='width:350px; color:white; background-color:#f36105' class='delete-btn'><b>Delete</b></button>";
                    echo "</form>";
                    echo "</div></div>";
                }
            } else {
                echo "No training plan found.";
            }
            ?>
        </div>
    </div>
    <!-- Gallery Section End -->

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
