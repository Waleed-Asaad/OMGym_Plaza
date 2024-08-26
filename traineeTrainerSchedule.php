<?php
session_start();
include 'connection.php';

// Define the refValues function
function refValues($arr) {
    if (strnatcmp(phpversion(), '5.3') >= 0) {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }
    return $arr;
}

function change($hour, $day, $conn) {

    $user_email = $_SESSION['userEmail'];
    // Retrieve user and trainee details
    $select = "SELECT * FROM user WHERE userEmail = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $user_id = $row['userId'];

    $select = "SELECT * FROM trainee WHERE userId = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $traineeRow = $result->fetch_assoc();
    $trainee_id = $traineeRow['traineeId'];
    $numberOfTrainings = $traineeRow['numberOfTrainings'];
    $actualNumberOfTrainings = $traineeRow['actualNumberOfTrainings'];

    if($numberOfTrainings > 0){
        // Check if trainee has remaining training slots
    if($numberOfTrainings > $actualNumberOfTrainings){
        // Retrieve trainer hour details
        $sql = "SELECT * FROM trainerHours WHERE hourId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $hour);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $available = ($row['available'] + 1) % 3;

        // Update the availability of the selected hour
        $sql_update = "UPDATE trainerHours SET available = ?, traineeId = ? WHERE hourId = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $available, $trainee_id, $hour);
        $stmt_update->execute();

        $available = ($available + 2) % 3;

        // Increment the actual number of trainings
        $actualNumberOfTrainings += 1;
        $sql_update = "UPDATE trainee SET actualNumberOfTrainings = ? WHERE traineeId = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $actualNumberOfTrainings, $trainee_id);
        $stmt_update->execute();

        // Retrieve the trainee's latest measurements
        $sql_measurements = "SELECT * FROM measurements WHERE traineeId = ? ORDER BY weightId DESC LIMIT 1";
        $stmt_measurements = $conn->prepare($sql_measurements);
        $stmt_measurements->bind_param("i", $trainee_id);
        $stmt_measurements->execute();
        $result_measurements = $stmt_measurements->get_result();
        $measurements = $result_measurements->fetch_assoc();

        // Prepare criteria for matching
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

        $select = "SELECT * FROM traineeDay WHERE traineeId = ? ORDER BY dayId ASC";
        $stmt = $conn->prepare($select);
        $stmt->bind_param("i", $trainee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $day_id = $row['dayId'];
            if ($day_id % 7 == $day % 7) {
                $sql = "SELECT * FROM traineeHours WHERE dayId = ? ORDER BY hourId ASC";
                $stmt_hours = $conn->prepare($sql);
                $stmt_hours->bind_param("i", $day_id);
                $stmt_hours->execute();
                $result_hours = $stmt_hours->get_result();
                while ($row_hours = $result_hours->fetch_assoc()) {
                    $hour_id = $row_hours['hourId'];
                    if ($hour_id % 12 == $hour % 12) {

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

    // If all plans are used, reset or handle accordingly
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

    // Use the training plan
    $training_plan_id = $training_plan['training_planId'];

    // Schedule the hour
    $sql_update = "UPDATE traineeHours SET scheduled = ?, training_planId = ? WHERE hourId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $available, $training_plan_id, $hour_id);
    $stmt_update->execute();

    // Update the trainer hour with the training plan
    $sql_update = "UPDATE trainerHours SET training_planId = ? WHERE hourId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $training_plan_id, $hour);
    $stmt_update->execute();

}
                }
            }
        }
    // Redirect or handle after scheduling
    header("Location: traineeTrainerSchedule.php");
    exit;
    } else {
        echo "<script type='text/javascript'>
                alert('You have reached the maximum number of trainings.');
                window.location.href = 'traineeTrainerSchedule.php';
              </script>";
    }
    }
    else{
        echo "<script type='text/javascript'>
                alert('You have to enter the number of trainings.');
                window.location.href = 'traineeTrainerSchedule.php';
              </script>";
    }
    
}

if (isset($_GET['change1']) && isset($_GET['change2'])) {
    change(intval($_GET['change1']), intval($_GET['change2']), $conn);
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
    include 'traineeMenu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Timetable</h2>
                        <div class="bt-option">
                            <a href="#">Home</a>
                            <span>Trainer Schedule</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Class Timetable Section Begin -->
    <section class="class-timetable-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title">
                        <h2>PICK CLASSES</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="class-timetable">
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>7:00 - 8:00</th>
                                    <th>8:00 - 9:00</th>
                                    <th>9:00- 10:00</th>
                                    <th>10:00-11:00</th>
                                    <th>11:00-12:00</th>
                                    <th>12:00-13:00</th>
                                    <th>13:00-14:00</th>
                                    <th>14:00-15:00</th>
                                    <th>15:00-16:00</th>
                                    <th>16:00-17:00</th>
                                    <th>17:00-18:00</th>
                                    <th>18:00-19:00</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                    <?php
                                    $user_email = $_SESSION['userEmail'];
                                    $select = "SELECT * FROM user WHERE userEmail = ?";
                                    $stmt = $conn->prepare($select);
                                    $stmt->bind_param("s", $user_email);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();
                                    $user_id = $row['userId'];
                                
                                    $select = "SELECT * FROM trainee WHERE userId = ?";
                                    $stmt = $conn->prepare($select);
                                    $stmt->bind_param("i", $user_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();
                                    
                                    $trainer_id = $row['trainerId'];


                                    $select = "SELECT * FROM trainerDay WHERE trainerId = ? ORDER BY dayId ASC";
                                    $stmt = $conn->prepare($select);
                                    $stmt->bind_param("i", $trainer_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $day_id = $row['dayId'];
                                        $day = $row['days'];
                                        echo "<tr><td style='font-size:20px; padding: 0' class='class-time'><b>$day</b></td>";
                                        $sql = "SELECT * FROM trainerHours WHERE dayId = ? ORDER BY hourId ASC";
                                        $stmt_hours = $conn->prepare($sql);
                                        $stmt_hours->bind_param("i", $day_id);
                                        $stmt_hours->execute();
                                        $result_hours = $stmt_hours->get_result();
                                        while ($row_hours = $result_hours->fetch_assoc()) {
                                            $hour_id = $row_hours['hourId'];
                                            $button_text = "";
                                            $button_color = "";
                                            switch ($row_hours['available']) {
                                                case 0:
                                                    $button_text = " / ";
                                                    $button_color = "#1B1212";
                                                    $text_color = "#e0f904";
                                                    break;
                                                case 1:
                                                    $button_text = "Valid";
                                                    $button_color = "#099105";
                                                    $text_color = "#e0f904";
                                                    break;
                                                case 2:
                                                    $button_text = "Booked";
                                                    $button_color = "#e95b5b";
                                                    $text_color = "#e0f904";
                                                    break;
                                            }
                                            if($row_hours['available']==0){
                                                echo "<td style='padding: 0; ' class='ts-meta'>
                                                <button style='padding: 0 ; height:52px; width: 100%; border-radius:10px 20px; background: $button_color; color: $text_color'>$button_text</button>
                                                </td>";
                                            }
                                            else if($row_hours['available']==2){
                                                echo "<td style='padding: 0; ' class='ts-meta'>
                                                <button style='padding: 0 ; height:52px; width: 100%; border-radius:10px 20px; background: $button_color; color: $text_color'>$button_text</button>
                                                </td>";
                                            }
                                            else{
                                                echo "<td style='padding: 0; ' class='ts-meta'>
                                                <button style='padding: 0 ; height:52px; width: 100%; border-radius:10px 20px; background: $button_color; color: $text_color' onclick='changeStatus($hour_id, $day_id);'>$button_text</button>
                                                </td>";
                                            }
                                            
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Class Timetable Section End -->

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

    <script>
        function changeStatus(hour, day) {
            window.location.href = "traineeTrainerSchedule.php?change1=" + hour + "&change2=" + day;
        }
    </script>
</body>

</html>
