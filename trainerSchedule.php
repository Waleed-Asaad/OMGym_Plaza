<?php
session_start();
include 'connection.php';

// פונקציה שמשנה את הזמינות של שעה מסוימת לפי התאריך והשעה הנוכחיים
function change($hour, $day, $conn) {
    $sql = "SELECT * FROM trainerHours WHERE hourId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hour);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $trainee_id = $row['traineeId'];
    $availableTrainer = $row['available'];

    date_default_timezone_set('Asia/Jerusalem'); // קובע את אזור הזמן לישראל
    $current_hour = intval(date('H'));
    $current_day = date('w') + 1; // מקבל את היום הנוכחי (0 ליום ראשון, 6 לשבת) ומוסיף 1 כדי להתאים את הימים

    $trainer_hour = intval($row['hours']); // השעה המתוכננת של המאמן

    // בודק את מצב הזמינות הנוכחי ומעדכן בהתאם
    if ($row['available'] == 0) {
        $availableTrainer = 1; // אם המאמן אינו זמין, הופך אותו לזמין
    } else if ($row['available'] == 1) {
        $availableTrainer = 0; // אם המאמן זמין, הופך אותו ללא זמין
    } else {
        // בודק האם ניתן לבטל את האימון לפי הזמן שנותר
        if (!(($trainer_hour - $current_hour >= 0 && $trainer_hour - $current_hour <= 2) && ($day % 7) == $current_day)) {
            // מציג הודעת אישור על ביטול האימון
            echo "<script type='text/javascript'>
                if (confirm('Are you sure you want to cancel the training?')) {
                    window.location.href = 'trainerSchedule.php?cancel1=" . $hour . "&cancel2=" . $day . "';
                } else {
                    window.location.href = 'trainerSchedule.php';
                }
            </script>";
            return; // עוצר את הפונקציה אם המשתמש מאשר את הביטול
        } else {
            // אם לא ניתן לבטל את האימון, מציג הודעה
            echo "<script type='text/javascript'>
                alert('You can\'t cancel the training');
                window.location.href = 'trainerSchedule.php';
            </script>";
            return; // עוצר את הפונקציה אם הביטול לא אפשרי
        }
    }

    // מעדכן את הזמינות של המאמן בבסיס הנתונים
    $sql_update = "UPDATE trainerHours SET available = ? WHERE hourId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $availableTrainer, $hour);
    $stmt_update->execute();
    header("Location: trainerSchedule.php"); // מפנה את המשתמש לעמוד לוח הזמנים של המאמן לאחר העדכון
    exit;
}

// בודק אם יש בקשה לשינוי מצב זמינות ומפעיל את הפונקציה change בהתאם
if (isset($_GET['change1']) && isset($_GET['change2'])) {
    change(intval($_GET['change1']), intval($_GET['change2']), $conn);
}

// בודק אם יש בקשה לביטול אימון ומפעיל את הפונקציה cancelTraining בהתאם
if (isset($_GET['cancel1']) && isset($_GET['cancel2'])) {
    cancelTraining(intval($_GET['cancel1']), intval($_GET['cancel2']), $conn);
}

// פונקציה לביטול אימון מסוים
function cancelTraining($hour, $day, $conn) {
    $sql = "SELECT * FROM trainerHours WHERE hourId = ?"; // שאילתא לבחירת השעה המתאימה לפי ה-ID שלה
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hour);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $trainee_id = $row['traineeId']; // מקבל את ה-ID של המתאמן אם יש כזה

    // בודק אם יש יום תואם בלוח הזמנים של המתאמן
    $select = "SELECT * FROM traineeDay WHERE traineeId = ? ORDER BY dayId ASC";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $trainee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $day_id = $row['dayId'];
        if ($day_id % 7 == $day % 7) { // משווה את היום שנבחר ליום בלוח הזמנים
            $sql = "SELECT * FROM traineeHours WHERE dayId = ? ORDER BY hourId ASC";
            $stmt_hours = $conn->prepare($sql);
            $stmt_hours->bind_param("i", $day_id);
            $stmt_hours->execute();
            $result_hours = $stmt_hours->get_result();
            while ($row_hours = $result_hours->fetch_assoc()) {
                $hour_id = $row_hours['hourId'];
                if ($hour_id % 12 == $hour % 12) { // משווה את השעה שנבחרה לשעה בלוח הזמנים
                    // מבטל את האימון בלוח הזמנים של המתאמן
                    $sql_update = "UPDATE traineeHours SET scheduled = 0 WHERE hourId = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("i", $hour_id);
                    $stmt_update->execute();

                    // מסיר את המתאמן מהשעה בלוח הזמנים של המאמן
                    $sql_update = "UPDATE trainerHours SET traineeId = 0 WHERE hourId = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("i", $hour);
                    $stmt_update->execute();
                }
            }
        }
    }

    // מעדכן את מספר הביטולים של המאמן בבסיס הנתונים
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $user_id = $row['userId'];

    $select = "SELECT * FROM trainer WHERE userId = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cancel = $row['cancel'] + 1;
    $trainerName = $row['trainerName'];

    $select = "SELECT * FROM trainerDay WHERE dayId = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $day);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $dayName = $row['days'];

    $select = "SELECT * FROM trainerHours WHERE hourId = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("i", $hour);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $hourName = $row['hours'];

    $messageContent = "{$trainerName}: has cancel training on {$dayName} at {$hourName}:00";

    $sql1 = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, 0, ?, 0)";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("si", $messageContent, $trainee_id);
    $stmt->execute();

    $sql2 = "SELECT adminId FROM admin LIMIT 1";  // Assuming there's at least one admin
    $result = $conn->query($sql2);
    $admin = $result->fetch_assoc();
    $adminId = $admin['adminId'];

        
    $sql3 = "INSERT INTO admin_messages (content, readed, adminId) VALUES (?, 0, ?)";
    $stmt = $conn->prepare($sql3);
    $stmt->bind_param("si", $messageContent, $adminId);
    $stmt->execute();

    $sql_update1 = "UPDATE trainer SET cancel = ? WHERE trainerId = ?";
    $stmt_update1 = $conn->prepare($sql_update1);
    $stmt_update1->bind_param("ii", $cancel, $row['trainerId']);
    $stmt_update1->execute();

    // מעדכן את השעה בלוח הזמנים של המאמן כלא זמינה
    $sql_update2 = "UPDATE trainerHours SET available = 0 WHERE hourId = ?";
    $stmt_update2 = $conn->prepare($sql_update2);
    $stmt_update2->bind_param("i", $hour);
    $stmt_update2->execute();
}

// Handle fetching the training plan
if (isset($_GET['show_plan']) && isset($_GET['hour_id'])) {
    error_log("GET parameters received: show_plan = {$_GET['show_plan']}, hour_id = {$_GET['hour_id']}");
    $hour_id = intval($_GET['hour_id']);

    // Fetch the training plan image based on hour_id
    $sql_plan = "SELECT tp.planImage FROM training_plan tp 
                 INNER JOIN trainerHours th ON th.training_planId = tp.training_planId
                 WHERE th.hourId = ?";
    $stmt_plan = $conn->prepare($sql_plan);
    $stmt_plan->bind_param("i", $hour_id);
    $stmt_plan->execute();
    $result_plan = $stmt_plan->get_result();
    $plan = $result_plan->fetch_assoc();

    if ($plan) {
        echo json_encode($plan);
    } else {
        echo json_encode(['error' => 'No plan found for this hour.']);
    }
    exit;
} else {
    error_log("GET parameters not set.");
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

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb-text">
                    <h2>Timetable</h2>
                    <div class="bt-option">
                        <a href="./index.html">Home</a>
                        <span>My Schedule</span>
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
                    <h2>Update your Schedule</h2>
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
                            // שולף את פרטי המשתמש לפי המייל מהסשן
                            $user_email = $_SESSION['userEmail'];
                            $select = "SELECT * FROM user WHERE userEmail = ?";
                            $stmt = $conn->prepare($select);
                            $stmt->bind_param("s", $user_email);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $user_id = $row['userId'];

                            // שולף את פרטי המאמן לפי ה-userId של המשתמש
                            $select = "SELECT * FROM trainer WHERE userId = ?";
                            $stmt = $conn->prepare($select);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $trainer_id = $row['trainerId'];

                            // שולף את לוח הזמנים של המאמן לפי ה-trainerId ומסדר לפי ימים
                            $select = "SELECT * FROM trainerDay WHERE trainerId = ? ORDER BY dayId ASC";
                            $stmt = $conn->prepare($select);
                            $stmt->bind_param("i", $trainer_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                $day_id = $row['dayId'];
                                $day = $row['days'];
                                echo "<tr><td style='font-size:20px; padding: 0' class='class-time'><b>$day</b></td>";

                                // שולף את שעות העבודה של המאמן לפי היום
                                $sql = "SELECT * FROM trainerHours WHERE dayId = ? ORDER BY hourId ASC";
                                $stmt_hours = $conn->prepare($sql);
                                $stmt_hours->bind_param("i", $day_id);
                                $stmt_hours->execute();
                                $result_hours = $stmt_hours->get_result();
                                while ($row_hours = $result_hours->fetch_assoc()) {
                                    $hour_id = $row_hours['hourId'];
                                    
                                    $traineeId = $row_hours['traineeId'];
                                    $button_text = "";
                                    $button_color = "";
                                    $text_color = "";

                                    // קובע את הטקסט והצבע של הכפתור בהתאם לזמינות השעה
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
                                            $sql = "SELECT * FROM trainee WHERE traineeId = '$traineeId'";
                                            $traineeResult = mysqli_query($conn, $sql);
                                            $traineeRow = mysqli_fetch_assoc($traineeResult);
                                            $traineeName = $traineeRow['traineeName'];
                                            $button_text = $traineeName;
                                            $button_color = "#e95b5b";
                                            $text_color = "#e0f904";
                                            break;
                                    }

                                    if ($row_hours['available'] == 2) {
                                    // מציג את הכפתור בשעה המתאימה בטבלה
                                    
                                    echo "<td style='padding: 0; ' class='ts-meta'>
                                        <button style=' border-radius:10px 20px; padding: 0 ; height:26px; width: 100%; background: $button_color; color: $text_color' onclick='changeStatus($hour_id, $day_id);'>$button_text</button>";
                                    
                                    // הוספת כפתור להצגת תוכנית האימון
                                    
                                        echo "<button style=' border-radius:10px 20px; padding: 0 ; height:26px; width: 100%; background: $button_color; color: $text_color' onclick='showPlan($hour_id) '>View Plan</button>";
                                        echo "</td>";
                                    }
                                    else{
                                        echo "<td style='padding: 0; ' class='ts-meta'>
                                        <button style=' border-radius:10px 20px; padding: 0 ; height:52px; width: 100%; background: $button_color; color: $text_color' onclick='changeStatus($hour_id, $day_id);'>$button_text</button>";
                                    }

                                    echo "</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- Training Plan Display Area -->
                    <div id="training-plan" style="display:none; padding-top:20px;">
                        <h3>Training Plan Image</h3>
                        <img id="plan-image" src="" alt="Training Plan Image" style="max-width:100%;">
                    </div>
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
    // פונקציה לשינוי מצב הזמינות של שעה מסוימת על ידי שליחה ל-URL המתאים
    function changeStatus(hour, day) {
        window.location.href = "trainerSchedule.php?change1=" + hour + "&change2=" + day;
    }

    function showPlan(hour_id) {
    console.log("Fetching plan for hour_id: " + hour_id);
    // Fetch the training plan image using AJAX
    $.ajax({
        url: "trainerSchedule.php",
        method: "GET",
        data: { show_plan: true, hour_id: hour_id },
        dataType: "json",
        success: function (response) {
            console.log("Response received:", response);
            if (response.error) {
                $('#plan-image').attr('src', '').attr('alt', response.error);
            } else {
                // Display the training plan image
                var imagePath = "img/training_plans/" + response.planImage;
                $('#plan-image').attr('src', imagePath).attr('alt', 'Training Plan Image');
            }
            $('#training-plan').show();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching training plan:', error);
        }
    });
}
</script>
</body>
</html>
