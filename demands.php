<?php

include 'connection.php';
session_start();

function change1($traineeId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['userId'];

        $sql = "SELECT * FROM trainer WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $trainerId = $row['trainerId'];
            $trainerName = $row['trainerName'];

            $sql = "UPDATE trainee SET trainerId = ? WHERE traineeId = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $trainerId, $traineeId);
                $stmt->execute();

                $sql = "UPDATE demands SET status = 'accepted' WHERE traineeId = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $traineeId);
                $stmt->execute();

                $insert = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) 
                           VALUES (?, 0, 0, ?, 0)";
                $message = "$trainerName has accepted you as a trainee";
                $stmt = $conn->prepare($insert);
                $stmt->bind_param("si", $message, $traineeId);
                $stmt->execute();
            }
        }
    }
    header("Location: demands.php");
    exit;
}

if (isset($_GET['change1'])) {
    change1(intval($_GET['change1']), $conn);
}

function change2($traineeId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['userId'];

        $sql = "SELECT * FROM trainer WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $trainerId = $row['trainerId'];

            $sql = "UPDATE demands SET status = 'refused' WHERE traineeId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $traineeId);
            $stmt->execute();

            $insert = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) 
                       VALUES (?, 0, 0, ?, 0)";
            $message = "Sorry, you have not been accepted. Please try to pick another trainer";
            $stmt = $conn->prepare($insert);
            $stmt->bind_param("si", $message, $traineeId);
            $stmt->execute();
        }
    }
    header("Location: demands.php");
    exit;
}

if (isset($_GET['change2'])) {
    change2(intval($_GET['change2']), $conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    .trainers {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }
    .card img {
        width: 100%;
        height: auto;
    }
</style>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Shop">
    <meta name="keywords" content="Gym, Shop, HTML">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shopping Cart</title>

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
    <link rel="stylesheet" href="css/cart.css" type="text/css">
</head>
<body>

<?php include 'trainer_menu.php'; ?>

<section style="height:1000px" class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb-text">
                    <div class="trainers">
                        <?php
                            $email = $_SESSION['userEmail'];
                            $select = "SELECT * FROM user WHERE userEmail = ?";
                            $stmt = $conn->prepare($select);
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $user_id = $row['userId'];

                            $sql = "SELECT * FROM trainer WHERE userId = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $trainer_id = $row['trainerId'];

                            $sql = "SELECT * FROM demands WHERE trainerId = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $trainer_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $count = 0;

                            while ($row = $result->fetch_assoc()) {
                                if($row['status'] == 'wait'){
                                $count++;
                                }
                            }

                            if ($count > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    
                                    $count++;
                                    $trainee_id = $row['traineeId'];
                                    $sql = "SELECT * FROM trainee WHERE traineeId = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $trainee_id);
                                    $stmt->execute();
                                    $trainee_result = $stmt->get_result();
                                    $trainee_row = $trainee_result->fetch_assoc();
                                    $trainee_name = $trainee_row['traineeName'];
                                    $trainee_img = $trainee_row['traineeImg'];
                                    
                                    echo "
                                        <div class='card'>
                                        <a href='myTrainee.php?trainee_id=$trainee_id'>
                                            <div class='card'>
                                                <img src='img/trainees/$trainee_img' alt='$trainee_name'>
                                                <h3>$trainee_name</h3>
                                                
                                            </div>
                                        </a>
                                        <button style='padding: 0; width: 100%; background: #f36105; color: white' onclick='acceptTrainee(".$trainee_id.")'>ACCEPT</button>
                                        <button style='padding: 0; width: 100%; background: #f36105; color: white' onclick='refuseTrainee(".$trainee_id.")'>REFUSE</button>
                                        </div>
                                        ";
                                    
                                    
                                }
                            } else {
                                echo "<h1 style='color: #f36105;'>No demands found.</h1>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
    function acceptTrainee(traineeId) {
        window.location.href = "demands.php?change1=" + traineeId;
    }

    function refuseTrainee(traineeId) {
        window.location.href = "demands.php?change2=" + traineeId;
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.set-bg').forEach(function(element) {
            var bg = element.getAttribute('data-setbg');
            element.style.backgroundImage = 'url(' + bg + ')';
        });
    });
</script>

</body>
</html>