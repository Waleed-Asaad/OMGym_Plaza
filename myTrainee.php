<?php 
include "connection.php";
session_start();


if(isset($_POST['submit'])){
    // Retrieve form data
    $weight = $_POST['weight'];
    $hand = $_POST['hand'];
    $leg = $_POST['leg'];
    $abdominal = $_POST['abdominal'];
    $chest = $_POST['chest'];
    
    // Ensure trainee_id is provided
    $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

    if ($trainee_id > 0) {
        // Fetch trainee details to calculate BMI
        $select = "SELECT height FROM trainee WHERE traineeId = ?";
        $stmt = $conn->prepare($select);
        if ($stmt) {
            $stmt->bind_param("i", $trainee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $height = $row['height'];
                
                // Calculate BMI
                $bmi = round($weight / (($height / 100) ** 2), 1);

                // Update trainee's weight and BMI
                $update_sql = "UPDATE trainee SET weight = ?, bmi = ? WHERE traineeId = ?";
                $update_stmt = $conn->prepare($update_sql);
                if ($update_stmt) {
                    $update_stmt->bind_param("idi", $weight, $bmi, $trainee_id);
                    if ($update_stmt->execute()) {
                        echo "Trainee record updated successfully";
                    } else {
                        echo "Error updating trainee record: " . $update_stmt->error;
                    }
                    $update_stmt->close();
                } else {
                    echo "Error preparing update statement: " . $conn->error;
                }

                // Insert measurements
                $insert_sql = "INSERT INTO measurements (weight, hand, leg, abdominal, chest, traineeId) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                if ($insert_stmt) {
                    $insert_stmt->bind_param("iiiiii", $weight, $hand, $leg, $abdominal, $chest, $trainee_id);
                    if($insert_stmt->execute()){
                        echo "Measurements record inserted successfully";
                    } else {
                        echo "Error inserting measurements record: " . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                } else {
                    echo "Error preparing insert statement: " . $conn->error;
                }

            } else {
                echo "No trainee found with the given ID.";
            }
            $stmt->close();
        } else {
            echo "Error preparing select statement: " . $conn->error;
        }

    } else {
        echo "Invalid trainee ID.";
    }

    // Redirect back to the trainee's page
    header("Location: myTrainee.php?trainee_id=$trainee_id");
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
</head>

<body>
<?php
    include 'Trainer_menu.php';
?>

    <!-- Hero Section Begin -->
    <section class="hero-section">
        <div class="hs-slider owl-carousel">
            <div style="height:3000px" class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
            <div  class="container">
            
           
            <div  class="col-lg-12 col-md-8">
                <div class="ps-item">
                <?php
$trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

if ($trainee_id > 0) {
    // Query to get the specific trainee's details
    $sql = "SELECT * FROM trainee WHERE traineeId = '$trainee_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $meal_id = $row['meal_planId'];
        $training_id = $row['training_planId'];
        $muscle_building = $row['muscle_building'];
        $weight_loss = $row['weight_loss'];
        $strength = $row['strength'];
        $endurance = $row['endurance'];
        $body_building = $row['body_building'];
        $flexibility = $row['flexibility'];
    }
}

?>

<h3 style="font-size:40px"><?php echo isset($row['traineeName']) ? $row['traineeName'] : 'No name found'; ?> Personal Details</h3>

<ul>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">Weight:</span> <?php echo isset($row['weight']) ? $row['weight'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">Height:</span> <?php echo isset($row['height']) ? $row['height'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">BMI:</span> <?php echo isset($row['bmi']) ? $row['bmi'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">Age:</span> <?php echo isset($row['age']) ? $row['age'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">Gender:</span> <?php echo isset($row['gender']) ? $row['gender'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px"><span style="color: #f36105">Activity:</span> <?php echo isset($row['activity']) ? $row['activity'] : 'N/A'; ?></li>
    <div class="specialty">
                                    <li style="font-size:35px;margin-bottom: 5px;color: #f36105">goal:</li>
                                    <?php
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
                                    ?>
                                    </div>
    <?php
    if ($meal_id > 0) {
        $sql1 = "SELECT * FROM meal_plans WHERE meal_planId = '$meal_id'";
        $result1 = mysqli_query($conn, $sql1);
        if ($result1) {
            $row1 = mysqli_fetch_assoc($result1);
            $mealPlanImg = $row1['planImage'];
        }

        if (isset($mealPlanImg)) {
            echo "<li style='font-size:45px;margin-bottom: 5px;color:#f36105'>Meal plan: <br> <img style='margin-top: 20px' src='img/meal_plans/$mealPlanImg' alt='Meal Plan Image'></li>";
        } else {
            echo "<li style='font-size:25px;margin-bottom: 5px'>Meal plan: <br> There's no meal plan yet</li>";
        }
    } else {
        echo "<li style='font-size:25px;margin-bottom: 5px'>Meal plan: <br> There's no meal plan yet</li>";
    }
    ?>

    
</ul>
                    
                </div>
            </div>
            
        </div> 
            
            </div>
            <div style=" height:3000px" class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg" >
                <div  class="container">
                    <div class="row">
                        <div class="col-lg-12 offset-lg-12">
                            <div class="hi-text">
                            <div class="container"  >
                <div class="form-container" style="width:1100px;padding:5px 5px 5px 5px" >
                    <form action="" style="width:1100px; " method="post">
                         <h1 style="font-size:35px;margin-bottom: 0;">Insert <?php echo $row['traineeName'] ?> Measurements </h1>
                        <?php
                          if(isset($err)){
                             foreach($err as $err){
                               echo '<span class="error-msg">'.$err.'</span>';
                             };
                           };
                         ?>
                            <input type="number" name="weight" required placeholder="enter your weight">
                            <input type="number" name="hand" required placeholder="enter your hand">
                             <input type="number" name="leg" required placeholder="enter your leg">
                             <input type="number" name="abdominal" required placeholder="enter your abdominal">
                             <input type="number" name="chest" required placeholder="enter your chest">
                             <input type="submit" name="submit" value="Submit" class="form-btn">
                         </form>
                    </div>

                           <!-- ChoseUs Section Begin -->
    <section class="choseus-section spad">
        <div style=" width:1500px; " class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        
                        <h2 style="margin-right:100px" >RECENT MEASUREMENTS</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div style="margin-left:50px" class="col-lg-2 col-sm-6">
                    <div class="cs-item">
                        <h4>WEIGHT</h4>
                        <?php
                        $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
                        $sql = "SELECT * FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql); 
                        
                        if ($result) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                        <p style="font-size:20px;"><b><?php echo $row["weight"]; ?></b></p>'
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="cs-item">
                        <h4>HAND</h4>
                        <?php
                        $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
                        $sql = "SELECT * FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql); 
                        
                        if ($result) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <p style="font-size:20px;"><b><?php echo $row["hand"]; ?></b></p>'
                                <?php
                            }
                        }
                        ?>               
                             </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="cs-item">
                        <h4>LEG</h4>
                        <?php
                        $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
                        $sql = "SELECT * FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql); 
                        
                        if ($result) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <p style="font-size:20px;" ><b><?php echo $row["leg"]; ?></b></p>'
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="cs-item">
                        <h4>ABDOMINAL</h4>
                        <?php
                        $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
                        $sql = "SELECT * FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql); 
                        
                        if ($result) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <p style="font-size:20px;"><b><?php echo $row["abdominal"]; ?></b></p>'
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="cs-item">
                        <h4>CHEST</h4>
                        <?php
                        $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
                        $sql = "SELECT * FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 5";
                        $result = mysqli_query($conn, $sql); 
                        
                        if ($result) {
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <p style="font-size:20px;"><b><?php echo $row["chest"]; ?></b></p>'
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ChoseUs Section End -->

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