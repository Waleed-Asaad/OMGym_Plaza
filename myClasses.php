<?php
session_start();
include 'connection.php';

function change($hour, $day, $conn) {
    $sql = "SELECT * FROM traineeHours WHERE hourId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hour);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $scheduled = ($row['scheduled'] + 1) % 2;
    $sql_update = "UPDATE traineeHours SET scheduled = ? WHERE hourId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $scheduled, $hour);
    $stmt_update->execute();

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
        if ($day_id % 7 == $day % 7) {
            $sql = "SELECT * FROM trainerHours WHERE dayId = ? ORDER BY hourId ASC";
            $stmt_hours = $conn->prepare($sql);
            $stmt_hours->bind_param("i", $day_id);
            $stmt_hours->execute();
            $result_hours = $stmt_hours->get_result();
            while ($row_hours = $result_hours->fetch_assoc()) {
                $hour_id = $row_hours['hourId'];
                if ($hour_id % 12 == $hour % 12) {
                    $available = ($row['available'] + 1) % 3;
                    $sql_update = "UPDATE trainerHours SET  traineeId = 0, available = ? WHERE hourId = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ii", $available, $hour_id);
                    $stmt_update->execute();
                }
            }
        }
    }

    header("Location: myClasses.php");
    exit;
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
                            <a href="./index.html">Home</a>
                            <a href="#">Pages</a>
                            <span>Services</span>
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
                        <h2>MY CLASSES</h2>
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
                                    
                                    $trainee_id = $row['traineeId'];


                                    $select = "SELECT * FROM traineeDay WHERE traineeId = ? ORDER BY dayId ASC";
                                    $stmt = $conn->prepare($select);
                                    $stmt->bind_param("i", $trainee_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($row = $result->fetch_assoc()) {
                                        $day_id = $row['dayId'];
                                        $day = $row['days'];
                                        echo "<tr><td style='padding: 0' class='class-time'>$day</td>";
                                        $sql = "SELECT * FROM traineeHours WHERE dayId = ? ORDER BY hourId ASC";
                                        $stmt_hours = $conn->prepare($sql);
                                        $stmt_hours->bind_param("i", $day_id);
                                        $stmt_hours->execute();
                                        $result_hours = $stmt_hours->get_result();
                                        while ($row_hours = $result_hours->fetch_assoc()) {
                                            $hour_id = $row_hours['hourId'];
                                            $button_text = "";
                                            $button_color = "";
                                            switch ($row_hours['scheduled']) {
                                                
                                                case 1:
                                                    $button_text = "Booked";
                                                    $button_color = "#099105";
                                                    $text_color = "#e0f904";
                                                    break;
                                                
                                                case 0:
                                                    $button_text = " / ";
                                                    $button_color = "#0a0a0a";
                                                    $text_color = "#e0f904";
                                                    break;
                                            }
                                            if($row_hours['scheduled']==1){
                                                
                                                echo "<td style='padding: 0; ' class='ts-meta'>
                                                <button style='padding: 0 ; width: 100%; background: $button_color; color: $text_color' onclick='changeStatus($hour_id, $day_id);'>$button_text</button>
                                                </td>";
                                            }
                                            

                                            else{
                                                echo "<td style='padding: 0; ' class='ts-meta'>
                                                <button style='padding: 0 ; width: 100%; background: $button_color; color: $text_color'>$button_text</button>
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
            window.location.href = "myclasses.php?change1=" + hour + "&change2=" + day;
        }
    </script>
</body>

</html>