<?php 
include "connection.php";
session_start();

function change($mealPlanId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
    $result = mysqli_query($conn, $select); 
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];

    $sql = "UPDATE trainee SET meal_planId = ? WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $mealPlanId, $user_id);
        if($stmt->execute()){
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    header("Location:mealPlans.php");
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
<?php
    include 'traineeMenu.php';
?>

    <?php
    $email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$email'";
    $result = mysqli_query($conn, $select);
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];

    $sql = "SELECT t.planImage FROM trainee tr JOIN meal_plans t ON tr.meal_planId = t.meal_planId WHERE tr.userId = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $mealPlanImg = $row['planImage'];
    $height = $mealPlanImg ? 800 : 500;

    echo '
    <section class="breadcrumb-section set-bg" data-setbg="img/hero/hero-2.jpg" style="height:'.$height.'px">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>MY MEAL PLAN</h2>
                        <div class="gallery">
                            <div class="grid-sizer"></div>';

    if ($mealPlanImg) {
        echo '<div class="gs-item grid-wide set-bg" data-setbg="img/meal_plans/'.$mealPlanImg.'" style="width:370px; height:300px; margin-left:390px; margin-right:auto;">
                <a href="img/meal_plans/'.$mealPlanImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
              </div>';
    } else {
        echo '<h2>YOU DID NOT PICK A MEAL PLAN YET</h2>';
    }
    echo '
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    ?>
    <!-- Breadcrumb Section End -->

    <!-- Gallery Section Begin -->
    <div class="gallery-section" style="height:700px;">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <?php
            $sql = "SELECT * FROM trainee WHERE userId = '$user_id'";
            $result = mysqli_query($conn, $sql);
            $trainee_row = mysqli_fetch_array($result);

            // List of attributes to check for
            $attributes = ["weight_loss", "strength", "flexibility", "endurance", "muscle_building", "body_building"];
            $mealPlans = [];
            $trainee_bmi = $trainee_row['bmi'];

            $sql = "SELECT * FROM meal_plans";
            $result = mysqli_query($conn, $sql);
            while ($mealRow = mysqli_fetch_assoc($result)) {
                $score = 0;
                $total_attributes = 0;
                foreach ($attributes as $attribute) {
                    if ($mealRow[$attribute] == 1) {
                        $total_attributes++;
                        if ($trainee_row[$attribute] == 1) {
                            $score++;
                        }
                    }
                }

                // Calculate BMI difference and adjust score
                $bmi_difference = abs($mealRow['bmi'] - $trainee_bmi);
                $bmi_score = max(1 - ($bmi_difference / 100), 0);  // Ensure score doesn't go negative
                $score += $bmi_score;
                $percentage = number_format(($score / ($total_attributes + 1)) * 100, 2);

                if ($score > 0) {
                    $mealPlans[] = ['mealPlan' => $mealRow, 'percentage' => $percentage];
                }
            }

            if (!empty($mealPlans)) {
                // Sort meal plans by percentage in descending order
                usort($mealPlans, function($a, $b) {
                    return $b['percentage'] - $a['percentage'];
                });

                // Get the top 4 meal plans
                $top_mealPlans = array_slice($mealPlans, 0, 4);

                // Display the top 4 meal plans
                foreach ($top_mealPlans as $mealPlan) {
                    $mealPlanImg = $mealPlan['mealPlan']['planImage'];
                    $percentage = $mealPlan['percentage'];
                    
                    echo '<div class="gs-item grid-wide set-bg" data-setbg="img/meal_plans/'.$mealPlanImg.'" style="width:370px; height:300px; margin-bottom:400px;">
                            <a href="img/meal_plans/'.$mealPlanImg.'" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width:'.$percentage.'%;"></div>
                            </div>
                            <p style="font-size:20px; color:white;margin-left:30px">'.$percentage.'% Matches</p>';
                            foreach ($attributes as $attribute) {
                                if ($mealPlan['mealPlan'][$attribute] == 1 && $trainee_row[$attribute] == 1) {
                                    echo '<li style="font-size:25px;margin-bottom: 5px;color:white">'.ucwords(str_replace('_', ' ', $attribute)).'</li>';
                                }
                            }
                            echo '
                            <button style="padding: 0; width: 100%; background: #f36105; color: white" onclick="pickMealPlan('.$mealPlan['mealPlan']['meal_planId'].');">Pick This Meal Plan</button>
                          </div>';
                }
            } else {
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
        function pickMealPlan(mealPlanId) {
            window.location.href = "mealPlans.php?change=" + mealPlanId;
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
