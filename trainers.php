<?php 
include "connection.php";
session_start();

if (isset($_POST['submit'])) {
    $rating_star = $_POST['star-rating'];
    $trainer_id = $_POST['trainer-id'];
    $email = $_SESSION['userEmail'];

    // Get user data
    $select = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $select);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $user_id = $row['userId'];

        // Get trainee data
        $select = "SELECT * FROM trainee WHERE userId = '$user_id'";
        $result = mysqli_query($conn, $select);
        $row = mysqli_fetch_array($result);
        $traineeId = $row['traineeId'];

        // Check if the trainee has already rated this trainer
        $check_rating = "SELECT * FROM rating WHERE traineeId = '$traineeId' AND trainerId = '$trainer_id' ORDER BY date DESC LIMIT 1";
        $rating_result = mysqli_query($conn, $check_rating);
        $can_rate = true;

        if ($rating_row = mysqli_fetch_array($rating_result)) {
            $last_rating_date = $rating_row['date'];
            $current_date = date('Y-m-d');

            // Calculate the difference between current date and last rating date
            $date_diff = strtotime($current_date) - strtotime($last_rating_date);
            $days_passed = round($date_diff / (60 * 60 * 24));

            // Only allow rating if more than 30 days (1 month) have passed
            if ($days_passed < 30) {
                $can_rate = false;
            }
        }

        if ($can_rate) {
            // Update rating and rating_sum, rating_counter in the trainer table
            $sql = "SELECT raiting_sum, raiting_counter FROM trainer WHERE trainerId = '$trainer_id'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $row = mysqli_fetch_array($result);
                $raitingSum = $row['raiting_sum'] + $rating_star;
                $raitingCounter = $row['raiting_counter'] + 1;
                $rating = $raitingSum / $raitingCounter;

                $sql = "UPDATE trainer SET rating = ?, raiting_sum = ?, raiting_counter = ? WHERE trainerId = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("diii", $rating, $raitingSum, $raitingCounter, $trainer_id);
                    if ($stmt->execute()) {
                        // Insert or update the rating into the rating table
                        if ($rating_row) {
                            // Update existing rating record
                            $update_rating = "UPDATE rating SET rating = ?, date = ? WHERE ratingId = ?";
                            $stmt_update = $conn->prepare($update_rating);
                            $current_date = date('Y-m-d');
                            $stmt_update->bind_param("isi", $rating_star, $current_date, $rating_row['ratingId']);
                            $stmt_update->execute();
                        } else {
                            // Insert new rating record
                            
                            $insert_rating = "INSERT INTO rating (traineeId, trainerId, rating, date) VALUES (?, ?, ?, ?)";
                            $stmt_insert = $conn->prepare($insert_rating);
                            if ($stmt_insert === false) {
                                die("Error preparing the query: " . $conn->error);
                            }

                            $current_date = date('Y-m-d');  // Get the current date in 'Y-m-d' format
                            $stmt_insert->bind_param("iiis", $traineeId, $trainer_id, $rating_star, $current_date);

                            if ($stmt_insert->execute()) {
                                echo "New rating inserted successfully!";
                            } else {
                                die("Error executing query: " . $stmt_insert->error);
                            }
                        }
                        echo "Rating updated successfully";
                    } else {
                        echo "Error updating trainer's record: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error;
                }
            } else {
                echo "Error selecting trainer: " . mysqli_error($conn);
            }
        } else {
            echo "<script type='text/javascript'>
                alert('You have already rated this trainer in the last 30 days.');
                window.location.href = 'trainers.php';
            </script>";
        }
    } else {
        echo "Error selecting user: " . mysqli_error($conn);
    }

    
    exit;
}

function change($trainerId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
    $result = mysqli_query($conn, $select);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $user_id = $row['userId'];

        $sql = "SELECT * FROM trainee WHERE userId = '$user_id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $traineeId = $row['traineeId'];
        $traineeName = $row['traineeName'];

        $sql = "SELECT * FROM trainer WHERE trainerId = '$trainerId'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        
        $trainerName = $row['trainerName'];

        
        
        $insert = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) 
        VALUES (?, 0, 0, 0, ?)";
        $message = "{$traineeName} asks you to accept him as trainee";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("si", $message, $trainerId);
        $stmt->execute();

        
        
        $insert = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) 
        VALUES (?, 0, 0, ?, 0)";
        $message = "The demand has sent to {$trainerName}";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("si", $message, $traineeId);
        $stmt->execute();

        $insert = "INSERT INTO demands ( trainerId, traineeId, status) VALUES('$trainerId', '$traineeId', 'wait')";
        mysqli_query($conn, $insert);
    } else {
        echo "Error selecting user: " . mysqli_error($conn);
    }

    header("Location: trainers.php");
    exit;
}

if (isset($_GET['change'])) {
    change(intval($_GET['change']), $conn);
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
        .star-rating1 {
            position: relative;
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin: 0 -0.25rem;
        }

        .star-input {
            position: absolute;
            opacity: 0;
        }

        .star-label {
            cursor: pointer;
            color: grey;
            padding: 0 0.25rem;
            transition: color 0.15s;
        }

        .star-input:checked ~ label {
            color: gold;
        }

        .star-input:hover ~ label {
            color: goldenrod;
            transition: none;
        }

        .star-label:active {
            color: darkgoldenrod !important;
        }

        .star-input:focus-visible + label {
            outline-offset: 1px;
            outline: #4f46e5 solid 2px;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            font-size: 1.5rem;
            justify-content: center;
            padding: 0.3rem;
        }
        .star-rating span {
            cursor: default;
            color: grey;
            padding: 0 0.1rem;
        }
        .star-rating .checked {
            color: gold;
        }
        .progress-bar {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-bar-fill {
            height: 20px;
            background-color: #00FF00;
            width: 0%;
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>

<body>
    <?php include 'traineeMenu.php'; ?>

    <!-- Breadcrumb Section Begin -->
    <section style="height:1000px" class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2 style="margin-right:130px">MY TRAINER</h2>
                        <div class="gallery" style="align-items: center;">
                            <div class="grid-sizer"></div>
                            <?php
                                $email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $sql = "SELECT t.trainerImg, t.trainerName, t.rating, t.trainerId FROM trainee tr JOIN trainer t ON tr.trainerId = t.trainerId WHERE tr.userId = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $trainerImg = $row['trainerImg'];
                                $trainerName = $row['trainerName'];
                                $trainerRating = number_format($row['rating'], 2);

                                if ($trainerImg) {
                                    echo '<div style="width:300px; margin-left:350px; margin-right:auto;" class="gs-item grid-wide set-bg" data-setbg="img/team/'.$trainerImg.'">
                                            <a href="img/team/'.$trainerImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                                            <p style="font-size:20px; color:white;">'.$trainerName.'</p>
                                            <p style="font-size:20px; color:white;">Rating: '.$trainerRating.'</p>
                                            <div class="star-rating">';
                                                for ($i = 1; $i <= 5; $i++) {
                                                    $checked = ($i <= round($trainerRating)) ? "checked" : "";
                                                    echo '<span class="fa fa-star '.$checked.'"></span>';
                                                }
                                    echo '  </div >
                                            <form style="margin-top:10px" action="" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="trainer-id" value="'.$row['trainerId'].'">
                                                <input type="radio" class="star-input" id="sr-'.$row['trainerId'].'-5" name="star-rating" value="5" />
                                                <label for="sr-'.$row['trainerId'].'-5" class="star-label">★</label>
                                                <input type="radio" class="star-input" id="sr-'.$row['trainerId'].'-4" name="star-rating" value="4" />
                                                <label for="sr-'.$row['trainerId'].'-4" class="star-label">★</label>
                                                <input type="radio" class="star-input" id="sr-'.$row['trainerId'].'-3" name="star-rating" value="3" />
                                                <label for="sr-'.$row['trainerId'].'-3" class="star-label">★</label>
                                                <input type="radio" class="star-input" id="sr-'.$row['trainerId'].'-2" name="star-rating" value="2" />
                                                <label for="sr-'.$row['trainerId'].'-2" class="star-label">★</label>
                                                <input type="radio" class="star-input" id="sr-'.$row['trainerId'].'-1" name="star-rating" value="1" />
                                                <label for="sr-'.$row['trainerId'].'-1" class="star-label">★</label>
                                                <input type="submit" style="background: #f36105; color: white;" name="submit" value="Submit" class="form-btn">
                                            </form>
                                        </div>';
                                } else {
                                    echo '<h2>YOU DID NOT PICK A TRAINER YET</h2>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Gallery Section Begin -->
    <div class="gallery-section" style="height:1000px;">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <?php
                $email = $_SESSION['userEmail'];
                $select = "SELECT * FROM user WHERE userEmail = '$email'";
                $result = mysqli_query($conn, $select);
                $row = mysqli_fetch_array($result);
                $user_id = $row['userId'];

                $sql = "SELECT * FROM trainee WHERE userId = '$user_id'";
                $result = mysqli_query($conn, $sql);
                $trainee_row = mysqli_fetch_array($result);
                if($trainee_row['bmi']){
                    $trainee_bmi = $trainee_row['bmi'];
                
                // List of attributes to check for
                $attributes = ["strength", "flexibility", "endurance", "weight_loss", "muscle_building", "body_building"];
                

                $trainers = [];

                $sql = "SELECT * FROM trainer";
                $result = mysqli_query($conn, $sql);
                
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    $score = 0;
                    $total_attributes = 0;
                    foreach ($attributes as $attribute) {
                        if($row[$attribute] == 1)
                            $total_attributes++;

                        if ($row[$attribute] == 1 && $trainee_row[$attribute] == 1) {
                            $score++;
                        }
                    }
                    
                    
                    $trainerId = $row['trainerId'];
                    $traineeId = $trainee_row['traineeId'];

                    $sql = "SELECT * FROM demands WHERE trainerId = '$trainerId' AND traineeId = '$traineeId'";
                    $demandResult = mysqli_query($conn, $sql);
                    $demandRow = mysqli_fetch_array($demandResult);
                    $status = $demandRow['status'];
                    $bmi_difference = abs($row['bmi'] - $trainee_bmi);
                    $bmi_score = max(1 - ($bmi_difference / 100), 0);  // Ensure score doesn't go negative
                    $score += $bmi_score;
                    $percentage = number_format(($score / ($total_attributes + 1)) * 100, 2);
                    if ($score > 0 && $row['numOfTrainees'] < 5 && $demandRow['status'] != 'accepted' && $demandRow['status'] != 'wait') {
                        $trainers[] = ['trainer' => $row, 'percentage' => $percentage , 'numOfTrainees' => $row['numOfTrainees']];
                    }
                }

                if (!empty($trainers)){
                    // Sort trainers by percentage in descending order
                    usort($trainers, function($a, $b) {
                        return $b['percentage'] - $a['percentage'];
                    });

                    // Get the top 4 trainers
                    $top_trainers = array_slice($trainers, 0, 4);

                    
                    

                    // Display the top 4 trainers
                    foreach ($top_trainers as $trainer) {
                        $trainerImg = $trainer['trainer']['trainerImg'];
                        $trainerName = $trainer['trainer']['trainerName'];
                        $percentage = $trainer['percentage'];
                        

                        echo '<div style="width:300px;" class="gs-item grid-wide set-bg" data-setbg="img/team/'.$trainerImg.'">
                                <a href="img/team/'.$trainerImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                                
                                <p style="font-size:20px; color:white;margin-left:100px">'.$trainerName.'</p>
                                <div class="progress-bar">
                                    <div class="progress-bar-fill" style="width:'.$percentage.'%;"></div>
                                </div>
                                <p style="font-size:20px; color:white;margin-left:30px">'.$percentage.'% Matches</p>
                                <div style="margin-left:-100px" class="star-rating">';
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($i <= round($trainer['trainer']['rating'])) ? "checked" : "";
                                        echo '<span  class="fa fa-star '.$checked.'"></span>';
                                    }
                        echo '  </div>';
                        foreach ($attributes as $attribute) {
                            if ($trainer['trainer'][$attribute]==1 && $trainee_row[$attribute] == 1) {
                                echo '<li style="font-size:25px;margin-bottom: 5px;color:white">'.ucwords(str_replace('_', ' ', $attribute)).'</li>';
                            }
                        }

                              echo '
                              <p style="font-size:25px; color:white;margin-left:40px">Trainees: '.$trainer["numOfTrainees"].'</p>
                              <button style="padding: 0; width: 100%; background: #f36105; color: white" onclick="pickTrainer('.$trainer['trainer']['trainerId'].');">Send a Request</button>
                              </div>';
                    }
                }
                
                } else {
                    echo '<h1 style="margin-left:700px; color:white">NO MATCH</h1>';
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

    <script>
        function pickTrainer(trainerId) {
            window.location.href = "trainers.php?change=" + trainerId;
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