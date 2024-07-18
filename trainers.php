<?php 
include "connection.php";
session_start();

if (isset($_POST['submit'])) {
    echo "<h1>before</h1>";
    $rating_star = $_POST['star-rating-0'];
    echo "<h1>after</h1>";
    $email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $select);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $user_id = $row['userId'];

        $sql = "SELECT t.raiting_sum, t.raiting_counter, t.trainerId FROM trainee tr JOIN trainer t ON tr.trainerId = t.trainerId WHERE tr.userId = '$user_id'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $row = mysqli_fetch_array($result);
            $trainerId = $row['trainerId'];
            $raitingSum = $row['raiting_sum'] + $rating_star;
            $raitingCounter = $row['raiting_counter'] + 1;
            $rating = $raitingSum / $raitingCounter;

            $sql = "UPDATE trainer SET rating = ?, raiting_sum = ?, raiting_counter = ? WHERE trainerId = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("diii", $rating, $raitingSum, $raitingCounter, $trainerId);
                if ($stmt->execute()) {
                    echo "Record updated successfully";
                } else {
                    echo "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error selecting trainer: " . mysqli_error($conn);
        }
    } else {
        echo "Error selecting user: " . mysqli_error($conn);
    }

    header("Location: trainers.php");
    exit;
}

function change($trainerId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
    $result = mysqli_query($conn, $select);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $user_id = $row['userId'];

        $sql = "UPDATE trainee SET trainerId = ? WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $trainerId, $user_id);
            if ($stmt->execute()) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
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
        .star-rating {
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
    </style>
</head>

<body>
    <?php include 'traineeMenu.php'; ?>

    <!-- Breadcrumb Section Begin -->
    <section style="height:1000px" class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div  class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2 >MY TRAINER</h2>
                        <div class="gallery" style="align-items: center;">
                            <div class="grid-sizer"></div>
                            <?php
                                $email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $sql = "SELECT t.trainerImg, t.trainerName FROM trainee tr JOIN trainer t ON tr.trainerId = t.trainerId WHERE tr.userId = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $trainerImg = $row['trainerImg'];
                                $trainerName = $row['trainerName'];

                                if ($trainerImg) {
                                    echo '<div style="width:300px; margin-left:auto; margin-right:auto;" class="gs-item grid-wide set-bg" data-setbg="img/team/'.$trainerImg.'">
                                            <a href="img/team/'.$trainerImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                                            <p style="font-size:20px; color:white;">'.$trainerName.'</p>
                                            <div class="star-rating">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="radio" class="star-input" id="sr-0-5" name="star-rating-0" value="5" />
                                                    <label for="sr-0-5" class="star-label">★</label>
                                                    <input type="radio" class="star-input" id="sr-0-4" name="star-rating-0" value="4" />
                                                    <label for="sr-0-4" class="star-label">★</label>
                                                    <input type="radio" class="star-input" id="sr-0-3" name="star-rating-0" value="3" />
                                                    <label for="sr-0-3" class="star-label">★</label>
                                                    <input type="radio" class="star-input" id="sr-0-2" name="star-rating-0" value="2" />
                                                    <label for="sr-0-2" class="star-label">★</label>
                                                    <input type="radio" class="star-input" id="sr-0-1" name="star-rating-0" value="1" />
                                                    <label for="sr-0-1" class="star-label">★</label>
                                                    <input type="submit" style="background: #f36105; color: white;" name="submit" value="Submit" class="form-btn">
                                                </form>
                                            </div>
                                        </div>';
                                } else {
                                    echo '<h2>YOU DID NOT PICK A TRAINER YET</h2>';
                                }
                            ?>
                            <script>
                                for (const starRating of document.getElementsByClassName("star-rating")) {
                                    starRating.addEventListener("keydown", (e) => {
                                        let action;
                                        if (e.key === "ArrowRight" || e.key === "ArrowDown") {
                                            action = "next";
                                        } else if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
                                            action = "previous";
                                        } else {
                                            return;
                                        }

                                        e.preventDefault();

                                        const inputs = Array.from(starRating.querySelectorAll("input"));

                                        for (let i = 0; i < inputs.length; i++) {
                                            if (document.activeElement === inputs[i]) {
                                                // focus the next/previous element, since we have reversed the order of the elements we need to subtract on next and add on previous
                                                let focusToIndex = action === "next" ? i - 1 : i + 1;
                                                if (focusToIndex < 0) focusToIndex = inputs.length - 1;
                                                if (focusToIndex >= inputs.length) focusToIndex = 0;

                                                inputs[focusToIndex].focus();
                                                inputs[focusToIndex].checked = true;
                                                break;
                                            }
                                        }
                                    });
                                }
                            </script>
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
                

                // List of attributes to check for
                $attributes = ["strength", "flexibility", "endurance", "weight_loss", "muscle_building", "body_building"];

                $trainers = [];

                $sql = "SELECT * FROM trainer";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    $score = 0;
                    foreach ($attributes as $attribute) {
                        
                             if ($row[$attribute] == 1 && $trainee_row[$attribute] == 1) {
                                $score++;
                            }
                        
                            
                    }
                    if ($score > 0) {
                        $trainers[] = ['trainer' => $row, 'score' => $score];
                    }
                    
                }
                if (!empty($trainers)){
                    // Sort trainers by score in descending order
                usort($trainers, function($a, $b) {
                    return $b['score'] - $a['score'];
                });

                // Get the top 4 trainers
                $top_trainers = array_slice($trainers, 0, 4);

                // Display the top 4 trainers
                foreach ($top_trainers as $trainer) {
                    
                         $trainerImg = $trainer['trainer']['trainerImg'];
                         $muscle_building = $trainer['trainer']['muscle_building'];
                         $weight_loss = $trainer['trainer']['weight_loss'];
                         $strength = $trainer['trainer']['strength'];
                         $endurance = $trainer['trainer']['endurance'];
                         $body_building = $trainer['trainer']['body_building'];
                         $flexibility = $trainer['trainer']['flexibility'];

                    echo '<div style="width:300px;" class="gs-item grid-wide set-bg" data-setbg="img/team/'.$trainerImg.'">
                            <a href="img/team/'.$trainerImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                            <p style="font-size:20px; color:white;">'.$trainer["trainer"]["trainerName"].'</p>
                            <p style="font-size:20px; color:white;">'.$trainer["score"].' Matches</p>';
                            foreach ($attributes as $attribute) {
                                if ($trainer['trainer'][$attribute]==1 && $trainee_row[$attribute] == 1) {
                                    echo '<li style="font-size:25px;margin-bottom: 5px;color:white">'.ucwords(str_replace('_', ' ', $attribute)).'</li>';
                                }
                            }
                                    echo '
                            <button style="padding: 0; width: 100%; background: #f36105; color: white;" onclick="pickTrainer('.$trainer["trainer"]["trainerId"].');">Pick This Trainer</button>
                          </div>';
                    
                   
                }
                }
                else{
                    echo '<h1 style="margin-left:700px; color:white" >NO MATCH</h1>';
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