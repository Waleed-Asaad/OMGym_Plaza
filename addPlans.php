<?php 
include "connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $select);
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];

    echo '<h1>Form Submission Detected</h1>';

    $sql = "SELECT * FROM trainer WHERE userId = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $trainer_id = $row['trainerId'];

    // Handle Training Plan submission
    if (isset($_POST['submit1'])) {
        echo '<h1>Submit1 Detected</h1>';
        $target = "img/training_plans/" . basename($_FILES['image1']['name']);
        $image = $_FILES['image1']['name'];

        if ($_FILES['image1']['error'] === 0) {
            $muscle_building = isset($_POST['muscle_building1']) ? 1 : 0;
            $weight_loss = isset($_POST['weight_loss1']) ? 1 : 0;
            $strength = isset($_POST['strength1']) ? 1 : 0;
            $endurance = isset($_POST['endurance1']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility1']) ? 1 : 0;
            $body_building = isset($_POST['body_building1']) ? 1 : 0;

            $sql = "INSERT INTO training_plan (planImage, trainerId, muscle_building, weight_loss, strength, endurance, flexibility, body_building) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("siiiiiii", $image, $trainer_id, $muscle_building, $weight_loss, $strength, $endurance, $flexibility, $body_building);
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
        echo '<h1>Submit2 Detected</h1>';
        $target = "img/meal_plans/" . basename($_FILES['image2']['name']);
        $image = $_FILES['image2']['name'];

        if ($_FILES['image2']['error'] === 0) {
            $muscle_building = isset($_POST['muscle_building2']) ? 1 : 0;
            $weight_loss = isset($_POST['weight_loss2']) ? 1 : 0;
            $strength = isset($_POST['strength2']) ? 1 : 0;
            $endurance = isset($_POST['endurance2']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility2']) ? 1 : 0;
            $body_building = isset($_POST['body_building2']) ? 1 : 0;

            $sql = "INSERT INTO meal_plans (planImage, trainerId, muscle_building, weight_loss, strength, endurance, flexibility, body_building) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("siiiiiii", $image, $trainer_id, $muscle_building, $weight_loss, $strength, $endurance, $flexibility, $body_building);
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
                                    <div class="form-container" style="padding:5px 5px 5px 5px">
                                        <form action="" method="post" enctype="multipart/form-data" style="width:800px;">
                                            <h1 style="font-size:35px;margin-bottom: 0;">Add Training Plan</h1>
                                            <?php
                                                if(isset($err)){
                                                    foreach($err as $err){
                                                        echo '<span class="error-msg">'.$err.'</span>';
                                                    };
                                                };
                                            ?>
                                            <input type="file" id="image1" name="image1" accept="image/*" required>
                                            <div class="specialty">
                                                <label style="color:white">Specialty:</label><br>
                                                <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                                <input type="checkbox" id="muscle_building1" name="muscle_building1" value="muscle_building">
                                                <label for="weight_loss" style="color:white">Weight Loss</label><br>
                                                <input type="checkbox" id="weight_loss1" name="weight_loss1" value="weight_loss">
                                                <label for="strength" style="color:white">Strength</label><br>
                                                <input type="checkbox" id="strength1" name="strength1" value="strength">
                                                <label for="endurance" style="color:white">Endurance</label><br>
                                                <input type="checkbox" id="endurance1" name="endurance1" value="endurance">
                                                <label for="flexibility" style="color:white">Flexibility</label><br>
                                                <input type="checkbox" id="flexibility1" name="flexibility1" value="flexibility">
                                                <label for="body_building" style="color:white">Body Building</label><br>
                                                <input type="checkbox" id="body_building1" name="body_building1" value="body_building">
                                            </div>
                                            <input type="submit" name="submit1" value="Submit" class="form-btn">
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
                                    <div class="form-container" style="padding:5px 5px 5px 5px">
                                        <form action="" method="post" enctype="multipart/form-data" style="width:800px;">
                                            <h1 style="font-size:35px;margin-bottom: 0;">Add Meal Plan</h1>
                                            <?php
                                                if(isset($err)){
                                                    foreach($err as $err){
                                                        echo '<span class="error-msg">'.$err.'</span>';
                                                    };
                                                };
                                            ?>
                                            <input type="file" id="image2" name="image2" accept="image/*" required>
                                            <div class="specialty">
                                                <label style="color:white">Specialty:</label><br>
                                                <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                                <input type="checkbox" id="muscle_building" name="muscle_building2" value="muscle_building">
                                                <label for="weight_loss" style="color:white">Weight Loss</label><br>
                                                <input type="checkbox" id="weight_loss" name="weight_loss2" value="weight_loss">
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