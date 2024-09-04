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



// Deleting a meal plan
if (isset($_POST['delete'])) {
    $meal_planId = $_POST['delete'];
    $deleteSql = "DELETE FROM meal_plans WHERE meal_planId = $meal_planId";
    mysqli_query($conn, $deleteSql);

    $sql = "SELECT * FROM trainee WHERE meal_planId = ? ";
    $stmt_hours = $conn->prepare($sql);
    $stmt_hours->bind_param("i", $meal_planId);
    $stmt_hours->execute();
    $traineeResult = $stmt_hours->get_result();
                
    while ($traineeRow = $traineeResult->fetch_assoc()) {
        $traineeId = $traineeRow['traineeId'];
        $trainee_message = "Your meal plan has been deleted by the trainer, you have to choose a new one.";
        $insert_trainee_message_sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, 0, ?, 0)";
        $insert_trainee_message_stmt = $conn->prepare($insert_trainee_message_sql);
        $insert_trainee_message_stmt->bind_param("si", $trainee_message, $traineeId);
        $insert_trainee_message_stmt->execute();
        $insert_trainee_message_stmt->close();

        // Update the trainee table with NULL where meal_planId
        $update_plan_sql = "UPDATE trainee SET meal_planId = NULL WHERE traineeId = ?";
        $update_plan_stmt = $conn->prepare($update_plan_sql);
        $update_plan_stmt->bind_param("i", $traineeId);
        $update_plan_stmt->execute();
        $update_plan_stmt->close();
    }
    

    

    header("Location: trainerMealPlans.php");
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
            $sql = "SELECT * FROM meal_plans WHERE trainerId = '$trainerId'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $planImage = $row['planImage'];
                    
                    echo "<div class='gs-item grid-wide set-bg' data-setbg='img/meal_plans/$planImage' style='width:350px; height:250px;margin-bottom:50px; margin-right:15px'>";
                    echo "<a href='img/meal_plans/$planImage' class='thumb-icon image-popup'><i class='fa fa-picture-o'></i></a>";
                    echo "<div class='product-actions'>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='delete' value='{$row["meal_planId"]}'>";
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
