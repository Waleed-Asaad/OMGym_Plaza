<?php 
include "connection.php";
session_start();


if(isset($_POST['submit'])){
    
    $weight = $_POST['weight'];
    $hand = $_POST['hand'];
    $leg = $_POST['leg'];
    $abdominal = $_POST['abdominal'];
    $chest = $_POST['chest'];
    
    $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

    $sql = "INSERT INTO measurements (weight,hand,leg,abdominal,chest,traineeId) VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iiiiii", $weight, $hand, $leg, $abdominal, $chest, $trainee_id);
        if($stmt->execute()){
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

    header("Location:myTrainee.php?trainee_id=$trainee_id");
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
            <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
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
    }
}

?>

<h3 style="font-size:40px"><?php echo isset($row['traineeName']) ? $row['traineeName'] : 'No name found'; ?> Personal Details</h3>

<ul>
    <li style="font-size:25px;margin-bottom: 5px">Height: <?php echo isset($row['height']) ? $row['height'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px">Age: <?php echo isset($row['age']) ? $row['age'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px">Gender: <?php echo isset($row['gender']) ? $row['gender'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px">Activity: <?php echo isset($row['activity']) ? $row['activity'] : 'N/A'; ?></li>
    <li style="font-size:25px;margin-bottom: 5px">Goal: <?php echo isset($row['goal']) ? $row['goal'] : 'N/A'; ?></li>

    <?php
    if ($meal_id > 0) {
        $sql1 = "SELECT * FROM meal_plans WHERE meal_planId = '$meal_id'";
        $result1 = mysqli_query($conn, $sql1);
        if ($result1) {
            $row1 = mysqli_fetch_assoc($result1);
            $mealPlanImg = $row1['planImage'];
        }

        if (isset($mealPlanImg)) {
            echo "<li style='font-size:25px;margin-bottom: 5px'>Meal plan: <br> <img src='img/meal_plans/$mealPlanImg' alt='Meal Plan Image'></li>";
        } else {
            echo "<li style='font-size:25px;margin-bottom: 5px'>Meal plan: <br> There's no meal plan yet</li>";
        }
    } else {
        echo "<li style='font-size:25px;margin-bottom: 5px'>Meal plan: <br> There's no meal plan yet</li>";
    }
    ?>

    <?php
    if ($training_id > 0) {
        $sql2 = "SELECT * FROM training_plan WHERE training_planId = '$training_id'";
        $result2 = mysqli_query($conn, $sql2);
        if ($result2) {
            $row2 = mysqli_fetch_assoc($result2);
            $trainingPlanImg = $row2['planImage'];
        }

        if (isset($trainingPlanImg)) {
            echo "<li style='font-size:25px;margin-bottom: 5px'>Training plan: <br> <img src='img/training_plans/$trainingPlanImg' alt='Training Plan Image'></li>";
        } else {
            echo "<li style='font-size:25px;margin-bottom: 5px'>Training plan: <br> There's no training plan yet</li>";
        }
    } else {
        echo "<li style='font-size:25px;margin-bottom: 5px'>Training plan: <br> There's no training plan yet</li>";
    }
    ?>
</ul>
                    
                </div>
            </div>
            
        </div> 
            
            </div>
            <div style=" height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg" >
                <div  class="container">
                    <div class="row">
                        <div class="col-lg-12 offset-lg-12">
                            <div class="hi-text">
                            <div class="container"  ">
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