<?php 
include "connection.php";
session_start();

$user_email = $_SESSION['userEmail'];
$select = "SELECT * FROM user WHERE userEmail = ?";
$stmt_user = $conn->prepare($select);
$stmt_user->bind_param("s", $user_email);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$row_user = $result_user->fetch_assoc();
$user_id = $row_user['userId'];

// Get the trainer ID
$select_trainer = "SELECT * FROM trainer WHERE userId = ?";
$stmt_trainer = $conn->prepare($select_trainer);
$stmt_trainer->bind_param("i", $user_id);
$stmt_trainer->execute();
$result_trainer = $stmt_trainer->get_result();
$row_trainer = $result_trainer->fetch_assoc();
$trainerId = $row_trainer['trainerId'];

// Deleting a training plan
if (isset($_POST['delete'])) {
    $deleted_training_planId = $_POST['delete'];

    // Select trainee hours using the deleted training plan
    $sql_hours = "SELECT * FROM traineeHours WHERE training_planId = ?";
    $stmt_hours = $conn->prepare($sql_hours);
    $stmt_hours->bind_param("i", $deleted_training_planId);
    $stmt_hours->execute();
    $traineeHourResult = $stmt_hours->get_result();
                
    while ($traineeHourRow = $traineeHourResult->fetch_assoc()) {
        $traineeId = $traineeHourRow['traineeId'];
        $hourName = $traineeHourRow['hours'];
        $dayId = $traineeHourRow['dayId'];
        $hourId = $traineeHourRow['hourId'];

        // Get day name for the trainee
        $sql_day = "SELECT * FROM traineeDay WHERE dayId = ?";
        $stmt_day = $conn->prepare($sql_day);
        $stmt_day->bind_param("i", $dayId);
        $stmt_day->execute();
        $dayResult = $stmt_day->get_result();
        $dayRow = $dayResult->fetch_assoc();
        $dayName = $dayRow['days'];

        // Select the latest measurement for the trainee
        $sql_measurements = "SELECT * FROM measurements WHERE traineeId = ? ORDER BY weightId DESC LIMIT 1";
        $stmt_measurements = $conn->prepare($sql_measurements);
        $stmt_measurements->bind_param("i", $traineeId);
        $stmt_measurements->execute();
        $result_measurements = $stmt_measurements->get_result();
        $measurements = $result_measurements->fetch_assoc();

        // Prepare criteria for matching
        $sql_trainee = "SELECT * FROM trainee WHERE traineeId = ?";
        $stmt_trainee = $conn->prepare($sql_trainee);
        $stmt_trainee->bind_param("i", $traineeId);
        $stmt_trainee->execute();
        $result_trainee = $stmt_trainee->get_result();
        $traineeRow = $result_trainee->fetch_assoc();

        // Fetch trainee's fitness goals and measurements
        $bmi = $traineeRow['bmi'];
        $muscle_building = $traineeRow['muscle_building'];
        $endurance = $traineeRow['endurance'];
        $strength = $traineeRow['strength'];
        $body_building = $traineeRow['body_building'];
        $weight_loss = $traineeRow['weight_loss'];
        $flexibility = $traineeRow['flexibility'];
        $abdominal = $measurements['abdominal'];
        $hand = $measurements['hand'];
        $chest = $measurements['chest'];
        $leg = $measurements['leg'];

        // Check previously used plans
        $sql_used_plans = "
            SELECT DISTINCT training_planId 
            FROM traineeHours 
            WHERE traineeId = ? AND training_planId IS NOT NULL";
        
        $stmt_used_plans = $conn->prepare($sql_used_plans);
        $stmt_used_plans->bind_param("i", $traineeId);
        $stmt_used_plans->execute();
        $result_used_plans = $stmt_used_plans->get_result();
        
        $used_plan_ids = [];
        while ($used_plan_row = $result_used_plans->fetch_assoc()) {
            $used_plan_ids[] = $used_plan_row['training_planId'];
        }

        // SQL to get a new training plan
        if (count($used_plan_ids) > 0) {
            $used_plan_ids_placeholder = implode(',', array_fill(0, count($used_plan_ids), '?'));
            $sql_plan = "
                SELECT training_planId, 
                       (bmi_match + muscle_building + endurance + strength + body_building + weight_loss + flexibility + abdominal_match + hand_match + chest_match + leg_match) AS score 
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
        } else {
            $sql_plan = "
                SELECT training_planId, 
                       (bmi_match + muscle_building + endurance + strength + body_building + weight_loss + flexibility + abdominal_match + hand_match + chest_match + leg_match) AS score 
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
        }

        // Prepare the SQL statement
        $stmt_plan = $conn->prepare($sql_plan);

        if (count($used_plan_ids) > 0) {
            $types = str_repeat("i", 11) . str_repeat("i", count($used_plan_ids));
            $params = array_merge(
                [$bmi, $muscle_building, $endurance, $strength, $body_building, $weight_loss, $flexibility, $abdominal, $hand, $chest, $leg],
                $used_plan_ids
            );
            $stmt_plan->bind_param($types, ...$params);
        } else {
            $stmt_plan->bind_param("iiiiiiiiiii", $bmi, $muscle_building, $endurance, $strength, $body_building, $weight_loss, $flexibility, $abdominal, $hand, $chest, $leg);
        }

        // Execute the statement and get the result
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

        // Use the new training plan
        $training_plan_id = $training_plan['training_planId'];

        // Update the traineeHours table with the new training_planId
        $sql_update = "UPDATE traineeHours SET training_planId = ? WHERE hourId = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $training_plan_id, $hourId);
        $stmt_update->execute();

        // Select the trainer for the trainee and training plan
        $sql_trainer = "SELECT * FROM trainerHours WHERE traineeId = ? AND training_planId = ?";
        $stmt_trainer = $conn->prepare($sql_trainer);
        $stmt_trainer->bind_param("ii", $traineeId, $deleted_training_planId);
        $stmt_trainer->execute();
        $result_trainer = $stmt_trainer->get_result();
        $trainerRow = $result_trainer->fetch_assoc();
        $trainerDayId = $trainerRow['dayId'];

        $sql_trainer = "SELECT * FROM trainerDay WHERE dayId = ? ";
        $stmt_trainer = $conn->prepare($sql_trainer);
        $stmt_trainer->bind_param("i", $trainerDayId);
        $stmt_trainer->execute();
        $result_trainer = $stmt_trainer->get_result();
        $trainerRow = $result_trainer->fetch_assoc();
        $updatedTrainerID = $trainerRow['trainerId'];

        // Update the trainer hours with the new training plan
        $sql_update_trainer = "UPDATE trainerHours SET training_planId = ? WHERE traineeId = ? AND training_planId = ?";
        $stmt_update_trainer = $conn->prepare($sql_update_trainer);
        $stmt_update_trainer->bind_param("iii", $training_plan_id, $traineeId, $deleted_training_planId);
        $stmt_update_trainer->execute();

        // Send a message to the trainee and the trainer
        $trainee_message = "Your training plan on ".$dayName." at ".$hourName.":00 has been changed to a new plan.";
        $insert_trainee_message_sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, 0, ?, ?)";
        $insert_trainee_message_stmt = $conn->prepare($insert_trainee_message_sql);
        
        // Check if the prepare statement failed
        if (!$insert_trainee_message_stmt) {
            echo "Message query preparation failed: " . $conn->error;
        }

        $insert_trainee_message_stmt->bind_param("sii", $trainee_message, $traineeId, $updatedTrainerID);
        
        if (!$insert_trainee_message_stmt->execute()) {
            echo "Message insertion failed: " . $insert_trainee_message_stmt->error;
        } else {
            echo "Message sent successfully.";
        }
    }

    // Delete the training plan
    $deleteSql = "DELETE FROM training_plan WHERE training_planId = ?";
    $stmt_delete = $conn->prepare($deleteSql);
    $stmt_delete->bind_param("i", $deleted_training_planId);
    $stmt_delete->execute();

    header("Location: trainerTrainingPlans.php");
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
<?php include 'trainer_menu.php'; ?>
<section class="breadcrumb-section set-bg" data-setbg="img/hero/hero-2.jpg">
        <div class="container">
        </div>
</section>

<!-- Gallery Section Begin -->
<div class="gallery-section" style="height:2000px;">
    <div class="gallery">
        <div class="grid-sizer"></div>
        <?php
        $sql = "SELECT * FROM training_plan WHERE trainerId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $trainerId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $planImage = $row['planImage'];
                echo "<div class='gs-item grid-wide set-bg' data-setbg='img/training_plans/$planImage' style='width:350px; height:80px;margin-bottom:50px; margin-right:15px'>";
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
