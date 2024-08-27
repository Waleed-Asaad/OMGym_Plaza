<?php 
include "connection.php";
session_start();

$trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

// Check if a week has passed since the last measurement
$check_sql = "SELECT DATE(date) AS last_date FROM measurements WHERE traineeId = ? ORDER BY weightId DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
if ($check_stmt) {
    $check_stmt->bind_param("i", $trainee_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_date = new DateTime($row['last_date']);
        $current_date = new DateTime();
        $interval = $last_date->diff($current_date);

        if ($interval->days >= 7) {
            // Send a message to the trainer
            $select = "SELECT * FROM trainee WHERE traineeId = ?";
            $stmt = $conn->prepare($select);
            $stmt->bind_param("i", $trainee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $traineeName = $row['traineeName'];
            $trainer_id = $row['trainerId'];

            $message = "It has been a week since the last measurements were taken for ".$traineeName." . Please schedule a new measurement.";
            $insert_message_sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, 0, ?, ?)";
            $insert_message_stmt = $conn->prepare($insert_message_sql);
            $insert_message_stmt->bind_param("sii", $message, $trainee_id, $trainer_id);
            $insert_message_stmt->execute();
            $insert_message_stmt->close();
        }
    }
    $check_stmt->close();
}


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
        // Fetch trainee details to calculate BMI and retrieve relevant fields
        $select = "SELECT height, trainerId, weight_loss, strength, endurance, muscle_building, flexibility, body_building, bmi FROM trainee WHERE traineeId = ?";
        $stmt = $conn->prepare($select);
        if ($stmt) {
            $stmt->bind_param("i", $trainee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $height = $row['height'];
                $trainer_id = $row['trainerId'];
                
                $weight_loss = $row['weight_loss'];
                $strength = $row['strength'];
                $endurance = $row['endurance'];
                $muscle_building = $row['muscle_building'];
                $flexibility = $row['flexibility'];
                $body_building = $row['body_building'];
                
                // Calculate BMI
                $bmi = round($weight / (($height / 100) ** 2), 1);

                // Update trainee's weight and BMI
                $update_sql = "UPDATE trainee SET weight = ?, bmi = ? WHERE traineeId = ?";
                $update_stmt = $conn->prepare($update_sql);
                if ($update_stmt) {
                    $update_stmt->bind_param("idi", $weight, $bmi, $trainee_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }

                // Insert measurements
                $insert_sql = "INSERT INTO measurements (weight, hand, leg, abdominal, chest, traineeId) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                if ($insert_stmt) {
                    $insert_stmt->bind_param("iiiiii", $weight, $hand, $leg, $abdominal, $chest, $trainee_id);
                    $insert_stmt->execute();
                    $insert_stmt->close();
                }

                // Update the training_planId in the traineeHours table based on the new measurements and criteria
                $sql = "SELECT * FROM traineeHours WHERE traineeId = ? AND training_planId IS NOT NULL";
                $stmt_hours = $conn->prepare($sql);
                $stmt_hours->bind_param("i", $trainee_id);
                $stmt_hours->execute();
                $hourResult = $stmt_hours->get_result();
                
                while ($hourRow = $hourResult->fetch_assoc()) {
                    $training_planId = $hourRow['training_planId'];
                    $hourName = $hourRow['hours'];
                    $dayId = $hourRow['dayId'];

                    $select = "SELECT * FROM traineeDay WHERE dayId = ?";
                    $day_stmt = $conn->prepare($select);
                    $day_stmt->bind_param("i", $dayId);
                    $day_stmt->execute();
                    $day_result = $day_stmt->get_result();
                    $dayRow = $day_result->fetch_assoc();
                    $dayName = $dayRow['days'];

                    $sql_used_plans = "
                        SELECT DISTINCT training_planId 
                        FROM traineeHours 
                        WHERE traineeId = ? AND training_planId IS NOT NULL";
                    
                    $stmt_used_plans = $conn->prepare($sql_used_plans);
                    $stmt_used_plans->bind_param("i", $trainee_id);
                    $stmt_used_plans->execute();
                    $result_used_plans = $stmt_used_plans->get_result();

                    $used_plan_ids = [];
                    while ($used_plan_row = $result_used_plans->fetch_assoc()) {
                        $used_plan_ids[] = $used_plan_row['training_planId'];
                    }

                    if (count($used_plan_ids) > 0) {
                        $used_plan_ids_placeholder = implode(',', array_fill(0, count($used_plan_ids), '?'));
                        $sql_plan = "
                            SELECT training_planId, 
                                (bmi_match * 1 + muscle_building * 1 + endurance * 1 + strength * 1 + body_building * 1 + weight_loss * 1 + flexibility * 1 + abdominal_match * 1 + hand_match * 1 + chest_match * 1 + leg_match * 1) AS score 
                            FROM (
                                SELECT training_planId,
                                    (bmi = ?) AS bmi_match,
                                    (muscle_building = ?) AS muscle_building,
                                    (endurance = ?) AS endurance,
                                    (strength = ?) AS strength,
                                    (body_building = ?) AS body_building,
                                    (weight_loss = ?) AS weight_loss,
                                    (flexibility = ?) AS flexibility,
                                    (abdominal <= ?) AS abdominal_match,
                                    (hand <= ?) AS hand_match,
                                    (chest <= ?) AS chest_match,
                                    (leg <= ?) AS leg_match
                                FROM training_plan 
                                WHERE training_planId NOT IN ($used_plan_ids_placeholder)
                            ) AS matches
                            ORDER BY score DESC 
                            LIMIT 1";
                        
                        $stmt_plan = $conn->prepare($sql_plan);
                        $types = str_repeat("i", 11) . str_repeat("i", count($used_plan_ids));
                        $params = array_merge(
                            [$bmi, $muscle_building, $endurance, $strength, $body_building, $weight_loss, $flexibility, $abdominal, $hand, $chest, $leg],
                            $used_plan_ids
                        );
                        $stmt_plan->bind_param($types, ...$params);
                    } else {
                        $sql_plan = "
                            SELECT training_planId, 
                                (bmi_match * 1 + muscle_building * 1 + endurance * 1 + strength * 1 + body_building * 1 + weight_loss * 1 + flexibility * 1 + abdominal_match * 1 + hand_match * 1 + chest_match * 1 + leg_match * 1) AS score 
                            FROM (
                                SELECT training_planId,
                                    (bmi = ?) AS bmi_match,
                                    (muscle_building = ?) AS muscle_building,
                                    (endurance = ?) AS endurance,
                                    (strength = ?) AS strength,
                                    (body_building = ?) AS body_building,
                                    (weight_loss = ?) AS weight_loss,
                                    (flexibility = ?) AS flexibility,
                                    (abdominal <= ?) AS abdominal_match,
                                    (hand <= ?) AS hand_match,
                                    (chest <= ?) AS chest_match,
                                    (leg <= ?) AS leg_match
                                FROM training_plan
                            ) AS matches
                            ORDER BY score DESC 
                            LIMIT 1";
                        
                        $stmt_plan = $conn->prepare($sql_plan);
                        $stmt_plan->bind_param("iiiiiiiiiii", $bmi, $muscle_building, $endurance, $strength, $body_building, $weight_loss, $flexibility, $abdominal, $hand, $chest, $leg);
                    }

                    $stmt_plan->execute();
                    $result_plan = $stmt_plan->get_result();
                    $training_plan = $result_plan->fetch_assoc();

                    // If no training plan is found
                    if (!$training_plan) {
                        echo "<script type='text/javascript'>
                                alert('No suitable training plan available.');
                                window.location.href = 'traineeTrainerSchedule.php';
                            </script>";
                        exit;
                    }

                    $new_plan_id = $training_plan['training_planId'];

                    // Update the traineeHours table with the new training_planId
                    $update_plan_sql = "UPDATE traineeHours SET training_planId = ? WHERE traineeId = ? AND training_planId = ?";
                    $update_plan_stmt = $conn->prepare($update_plan_sql);
                    $update_plan_stmt->bind_param("iii", $new_plan_id, $trainee_id, $training_planId);
                    $update_plan_stmt->execute();
                    $update_plan_stmt->close();

                    // Send a message to the trainee
                    $trainee_message = "Your training plan on ".$dayName." at ".$hourName.":00 has been updated to a new plan.";
                    $insert_trainee_message_sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, 0, ?, ?)";
                    $insert_trainee_message_stmt = $conn->prepare($insert_trainee_message_sql);
                    $insert_trainee_message_stmt->bind_param("sii", $trainee_message, $trainee_id, $trainer_id);
                    $insert_trainee_message_stmt->execute();
                    $insert_trainee_message_stmt->close();

                    $stmt_plan->close();
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

// Fetch the latest 10 measurements for the graph
$trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;
$sql = "SELECT DATE(date) AS date_only, weight, hand, leg, abdominal, chest FROM measurements WHERE traineeId='$trainee_id' ORDER BY weightId DESC LIMIT 10";
$result = mysqli_query($conn, $sql);

$dates = [];
$weights = [];
$hands = [];
$legs = [];
$abdominals = [];
$chests = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $dates[] = $row["date_only"];
        $weights[] = $row["weight"];
        $hands[] = $row["hand"];
        $legs[] = $row["leg"];
        $abdominals[] = $row["abdominal"];
        $chests[] = $row["chest"];
    }
}

$dates = array_reverse($dates);
$weights = array_reverse($weights);
$hands = array_reverse($hands);
$legs = array_reverse($legs);
$abdominals = array_reverse($abdominals);
$chests = array_reverse($chests);
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        canvas {
            background-color: white;
        }
    </style>
</head>

<body>
<?php include 'Trainer_menu.php'; ?>

    <!-- Hero Section Begin -->
    <section class="hero-section">
        <div class="hs-slider owl-carousel">
            <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-1.jpg">
                <div class="container">
                    <div class="col-lg-12 col-md-8">
                        <div class="ps-item">
                            <?php
                            $trainee_id = isset($_GET['trainee_id']) ? intval($_GET['trainee_id']) : 0;

                            if ($trainee_id > 0) {
                                // Query to get the specific trainee's details
                                $sql = "SELECT * FROM trainee WHERE traineeId = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $trainee_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($result) {
                                    $row = $result->fetch_assoc();
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
                                    <li style="font-size:35px;margin-bottom: 5px;color: #f36105;">Goal:</li>
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
                                    $sql1 = "SELECT * FROM meal_plans WHERE meal_planId = ?";
                                    $stmt1 = $conn->prepare($sql1);
                                    $stmt1->bind_param("i", $meal_id);
                                    $stmt1->execute();
                                    $result1 = $stmt1->get_result();
                                    
                                    if ($result1) {
                                        $row1 = $result1->fetch_assoc();
                                        $mealPlanImg = $row1['planImage'];
                                    }

                                    if (isset($mealPlanImg)) {
                                        echo "<li style='font-size:45px;margin-bottom: 5px;color:#f36105'>Meal plan: <br> <div class='gs-item grid-wide set-bg' data-setbg='img/meal_plans/".$mealPlanImg."' style='width:380px; height:300px; margin-left:340px;margin-top:20px'>
                                        <a href='img/meal_plans/".$mealPlanImg."' style=' margin-left:450px;margin-top:20px' class='thumb-icon image-popup'><i class='fa fa-picture-o'></i></a></li>";
                                    } else {
                                        echo "<li style='font-size:25px;margin-bottom: 5px;color: #f36105'>Meal plan: <br> There's no meal plan yet</li>";
                                    }
                                } else {
                                    echo "<li style='font-size:25px;margin-bottom: 5px;color: #f36105'>Meal plan: <br> There's no meal plan yet</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div> 
            </div>
            
            <div style="height:1500px" class="hs-item set-bg" data-setbg="img/hero/hero-2.jpg" >
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 offset-lg-12">
                            <div class="hi-text">
                                <div class="container">
                                    <div class="form-container" style="width:1100px;padding:5px 5px 5px 5px">
                                        <form action="" style="width:1100px;" method="post">
                                            <h1 style="font-size:35px;margin-bottom: 0;">Insert <?php echo $row['traineeName'] ?> Measurements</h1>
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
                                        <div style="width:1500px;" class="container">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="section-title">
                                                        <h2 style="margin-right:100px;color: #f36105;">RECENT MEASUREMENTS</h2>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div style="margin-left:10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">DATE</h4>
                                                        <?php
                                                            // Displaying the dates
                                                            if ($result) {
                                                                $dates = array_reverse($dates);
                                                                $dates = array_slice($dates, 0, 5);
                                                                foreach($dates as $date) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $date; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div style="margin-left:-10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">WEIGHT</h4>
                                                        <?php
                                                            if ($result) {
                                                                $weights = array_reverse($weights);
                                                                $weights = array_slice($weights, 0, 5);
                                                                foreach($weights as $weight) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $weight; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div style="margin-left:-10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">HAND</h4>
                                                        <?php
                                                            if ($result) {
                                                                $hands = array_reverse($hands);
                                                                $hands = array_slice($hands, 0, 5);
                                                                foreach($hands as $hand) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $hand; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div style="margin-left:-10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">LEG</h4>
                                                        <?php
                                                            if ($result) {
                                                                $legs = array_reverse($legs);
                                                                $legs = array_slice($legs, 0, 5);
                                                                foreach($legs as $leg) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $leg; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div style="margin-left:-10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">ABDOMINAL</h4>
                                                        <?php
                                                            if ($result) {
                                                                $abdominals = array_reverse($abdominals);
                                                                $abdominals = array_slice($abdominals, 0, 5);
                                                                foreach($abdominals as $abdominal) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $abdominal; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div style="margin-left:-10px" class="col-lg-2 col-sm-6">
                                                    <div class="cs-item">
                                                        <h4 style="color: #f36105;">CHEST</h4>
                                                        <?php
                                                            if ($result) {
                                                                $chests = array_reverse($chests);
                                                                $chests = array_slice($chests, 0, 5);
                                                                foreach($chests as $chest) {
                                                        ?>
                                                        <p style="font-size:20px; margin-bottom:38px"><b><?php echo $chest; ?></b></p>
                                                        <?php
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   </div> 
                                    </section>
                                        <!-- Graph Section -->
                                        <section class="choseus-section spad">
                                        <div style="width:1500px;" class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="section-title">
                                                    <h2 style=" color: #f36105;">PROGRESS GRAPH</h2>
                                                </div>
                                                <canvas id="measurementsChart" width="400" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                    

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

    <!-- Chart.js Script to Render the Graph -->
    <script>
        const ctx = document.getElementById('measurementsChart').getContext('2d');
        const measurementsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [
                    {
                        label: 'Weight',
                        data: <?php echo json_encode($weights); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Hand',
                        data: <?php echo json_encode($hands); ?>,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Leg',
                        data: <?php echo json_encode($legs); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Abdominal',
                        data: <?php echo json_encode($abdominals); ?>,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Chest',
                        data: <?php echo json_encode($chests); ?>,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Trainee Measurement Progress',
                        color: '#f36105',
                        font: {
                            size: 30  // Set the font size here
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>
