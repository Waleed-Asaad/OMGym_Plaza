<?php 
include "connection.php";
session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the user's email from the session and fetch the userId from the database
    $email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $user_id = $row['userId'];

    // Fetch the trainerId based on the userId
    $sql = "SELECT * FROM trainer WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $trainer_id = $row['trainerId'];

    // Handle adding a training plan
    if (isset($_POST['submit1'])) {
        $target = "img/training_plans/" . basename($_FILES['image1']['name']);
        $image = $_FILES['image1']['name'];

        // Check if the file upload was successful
        if ($_FILES['image1']['error'] === 0) {
            // Determine the selected options and set appropriate values
            $muscle_building = isset($_POST['muscle_building1']) ? 1 : 0;
            $weight_loss = isset($_POST['weight_loss1']) ? 1 : 0;
            $strength = isset($_POST['strength1']) ? 1 : 0;
            $endurance = isset($_POST['endurance1']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility1']) ? 1 : 0;
            $body_building = isset($_POST['body_building1']) ? 1 : 0;
            $abdominal = $_POST['abdominal1'];
            $hand = $_POST['hand1'];
            $leg = $_POST['leg1'];
            $chest = $_POST['chest1'];
            $bmi = $_POST['bmi1'];

            // Query to insert the training plan into the database
            $sql = "INSERT INTO training_plan (planImage, trainerId, muscle_building, weight_loss, strength, endurance, flexibility, body_building, bmi, abdominal, hand, leg, chest) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                // Bind parameters and execute the query
                $stmt->bind_param("siiiiiiidiiii", $image, $trainer_id, $muscle_building, $weight_loss, $strength, $endurance, $flexibility, $body_building, $bmi, $abdominal, $hand, $leg, $chest);
                if ($stmt->execute()) {
                    move_uploaded_file($_FILES['image1']['tmp_name'], $target); // Move the uploaded file to the target directory
                    echo "Training plan added successfully";
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
        header("Location:addPlans.php"); // Redirect the user to the addPlans page after submission
        exit;
    }

    // Handle adding a meal plan
    if (isset($_POST['submit2'])) {
        $target = "img/meal_plans/" . basename($_FILES['image2']['name']);
        $image = $_FILES['image2']['name'];

        // Check if the file upload was successful
        if ($_FILES['image2']['error'] === 0) {
            // Determine the selected options and set appropriate values
            $muscle_building = isset($_POST['muscle_building2']) ? 1 : 0;
            $weight_loss = isset($_POST['weight_loss2']) ? 1 : 0;
            $strength = isset($_POST['strength2']) ? 1 : 0;
            $endurance = isset($_POST['endurance2']) ? 1 : 0;
            $flexibility = isset($_POST['flexibility2']) ? 1 : 0;
            $body_building = isset($_POST['body_building2']) ? 1 : 0;
            $bmi = $_POST['bmi2'];

            // Query to insert the meal plan into the database
            $sql = "INSERT INTO meal_plans (planImage, trainerId, muscle_building, weight_loss, strength, endurance, flexibility, body_building, bmi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                // Bind parameters and execute the query
                $stmt->bind_param("siiiiiiid", $image, $trainer_id, $muscle_building, $weight_loss, $strength, $endurance, $flexibility, $body_building, $bmi);
                if ($stmt->execute()) {
                    move_uploaded_file($_FILES['image2']['tmp_name'], $target); // Move the uploaded file to the target directory
                    echo "Meal plan added successfully";
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

    <!-- Google Fonts -->
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
        #image1 {
            background-color: black;
            color: white;
        }

        #image2 {
            background-color: black;
            color: white;
        }
    </style>
</head>

<body>
<?php
    include 'Trainer_menu.php'; // Include the trainer menu
?>

<!-- Hero Section Begin -->
<section class="hero-section">
    <div class="hs-slider owl-carousel">
        <!-- First slide for adding a training plan -->
        <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
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
                                        <!-- Training plan file upload area -->
                                        <p style="color: #f36105;">Add Profile Photo:</p>
                                        <input type="file" style="background: #f36105; color: white;" id="image1" name="image1" accept="image/*" required>
                                        
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add BMI:</p>
                                            <input type="number" id="bmi1" name="bmi1" placeholder="Enter BMI" min="0" step="0.1" required>
                                        </div>  
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add Abdominal Size:</p>
                                            <input type="number" id="abdominal1" name="abdominal1" placeholder="Enter Abdominal Size" min="0" required>
                                        </div>    
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add Hand Size:</p>
                                            <input type="number" id="hand1" name="hand1" placeholder="Enter Hand Size" min="0" required>
                                        </div>    
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add Leg Size:</p>
                                            <input type="number" id="leg1" name="leg1" placeholder="Enter Leg Size" min="0" required>
                                        </div> 
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add Chest Size:</p>
                                            <input type="number" id="chest1" name="chest1" placeholder="Enter Chest Size" min="0" required>
                                        </div>
                                        <!-- Specialty selection area -->
                                        <div class="specialty">
                                            <label style="color:#f36105">Specialty:</label><br>
                                            <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                            <input type="checkbox" id="muscle_building1" name="muscle_building1" value="1"><br>
                                            <label for="weight_loss" style="color:white">Weight Loss</label><br>
                                            <input type="checkbox" id="weight_loss1" name="weight_loss1" value="1"><br>
                                            <label for="strength" style="color:white">Strength</label><br>
                                            <input type="checkbox" id="strength1" name="strength1" value="1"><br>
                                            <label for="endurance" style="color:white">Endurance</label><br>
                                            <input type="checkbox" id="endurance1" name="endurance1" value="1"><br>
                                            <label for="flexibility" style="color:white">Flexibility</label><br>
                                            <input type="checkbox" id="flexibility1" name="flexibility1" value="1"><br>
                                            <label for="body_building" style="color:white">Body Building</label><br>
                                            <input type="checkbox" id="body_building1" name="body_building1" value="1"><br>
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
        <!-- Second slide for adding a meal plan -->
        <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg">
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
                                        <!-- Meal plan file upload area -->
                                        <p style="color: #f36105;">Add Profile Photo:</p>
                                        <input type="file" style="background: #f36105; color: white;" id="image2" name="image2" accept="image/*" required>
                                        
                                        <div style="display: inline-flex;width:550px">
                                            <p style="color: #f36105; width:200px">Add BMI:</p>
                                            <input type="number" id="bmi2" name="bmi2" placeholder="Enter BMI" min="0" step="0.1" required>
                                        </div>
                                        <!-- Specialty selection area -->
                                        <div class="specialty">
                                            <label style="color:white">Specialty:</label><br>
                                            <label for="muscle_building" style="color:white">Muscle Building</label><br>
                                            <input type="checkbox" id="muscle_building" name="muscle_building2" value="1"><br>
                                            <label for="weight_loss" style="color:white">Weight Loss</label><br>
                                            <input type="checkbox" id="weight_loss" name="weight_loss2" value="1"><br>
                                            <label for="strength" style="color:white">Strength</label><br>
                                            <input type="checkbox" id="strength" name="strength2" value="1"><br>
                                            <label for="endurance" style="color:white">Endurance</label><br>
                                            <input type="checkbox" id="endurance" name="endurance2" value="1"><br>
                                            <label for="flexibility" style="color:white">Flexibility</label><br>
                                            <input type="checkbox" id="flexibility" name="flexibility2" value="1"><br>
                                            <label for="body_building" style="color:white">Body Building</label><br>
                                            <input type="checkbox" id="body_building" name="body_building2" value="1"><br>
                                        </div>
                                        <!-- Submit button -->
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
