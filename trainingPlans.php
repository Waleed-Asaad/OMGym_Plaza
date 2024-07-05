<?php 
include "connection.php";
session_start();

function change($trainingPlanId, $conn) {
    $user_email = $_SESSION['userEmail'];
    $select = "SELECT * FROM user WHERE userEmail = '$user_email'";
    $result = mysqli_query($conn, $select); 
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];

    $sql = "UPDATE trainee SET training_planId = ? WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $trainingPlanId, $user_id);
        if($stmt->execute()){
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    header("Location:trainingPlans.php");
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

    
</head>

<body>
<?php
    include 'traineeMenu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section style="height:2000px" class="breadcrumb-section set-bg" data-setbg="img/hero/hero-1.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>MY MEAL PLAN</h2>
                        <div style="" class="gallery">
                        <div class="grid-sizer"></div>
                        <?php
                                $email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $sql = "SELECT t.planImage FROM trainee tr JOIN training_plan t ON tr.training_planId = t.training_planId WHERE tr.userId = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $trainingPlanImg = $row['planImage'];
                                
                                if($trainingPlanImg){
                                    echo '<div style="width:800px;height:1300px;  margin-left:200px" class="gs-item grid-wide set-bg" data-setbg="img/training_plans/'.$trainingPlanImg.'">
                                    <a href="img/training_plans/'.$trainingPlanImg.'"  class="thumb-icon image-popup"><i  class="fa fa-picture-o"></i></a>
                                 </div>';
                                }
                                else{
                                    echo '<h2>YOU DID NOT PICK TRAINING PLAN YET</h2>';
                                }
                                    ?>
                    </div>
                </div>
                </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Gallery Section Begin -->
    <div style="height:3000px" class="gallery-section">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <?php
                                $email = $_SESSION['userEmail'];
                                $select = "SELECT * FROM user WHERE userEmail = '$email'";
                                $result = mysqli_query($conn, $select);
                                $row = mysqli_fetch_array($result);
                                $user_id = $row['userId'];

                                $sql = "SELECT goal FROM trainee WHERE userId = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $goal = $row['goal'];

                                // List of attributes to check for
                                $attributes = ["strength", "flexibility", "endurance", "weight loss", "muscle building", "body building"];

                                $trainers = [];

                                $sql = "SELECT * FROM training_plan";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $score = 0;
                                    foreach ($attributes as $attribute) {
                                         if (strpos($goal, $attribute) !== false) {
                                            if($attribute == "weight loss" )
                                            {
                                                if ($row['weight_loss'] == 1) {
                                                    $score++;
                                            }

                                        }
                                            else if( $attribute == "muscle building" || $attribute == "body building")
                                            {
                                                if ($row['muscle_building'] == 1) {
                                                    $score++;
                                            }
                                        }
                                            else if(  $attribute == "body building")
                                            {
                                                if ($row['body_building'] == 1) {
                                                    $score++;
                                            }
                                        }
                                            else if ($row[$attribute] == 1) {
                                               $score++;
                                           }
                                        }
                                     }
                                      $trainingPlans[] = ['trainingPlan' => $row, 'score' => $score];
                                }

                                // Sort training plans by score in descending order
                                usort($trainingPlans, function($a, $b) {
                                    return $b['score'] - $a['score'];
                                });

                                // Get the top 4 trainingPlans
                                $top_trainingPlans = array_slice($trainingPlans, 0, 4);

                                // Display the top 4 trainingPlans
                                
                                foreach ($top_trainingPlans as $trainingPlan) {
                                   $trainingPlanImg = $trainingPlan['trainingPlan']['planImage'];
                                   echo '
                                        <div style="width:750px;height:1200px;margin-top:100px" class="gs-item grid-wide set-bg" data-setbg="img/training_plans/'.$trainingPlanImg.'">
                                         <a href="img/training_plans/'.$trainingPlanImg.'"  class="thumb-icon image-popup"><i style="width:2000px" class="fa fa-picture-o"></i></a>
                                         <p style="font-size:20px ; color:white">'.$trainingPlan["score"].' Matches</p>
                                        <button style="padding: 0; width: 100%; background: #f36105; color: white" onclick="picktrainingPlan('.$trainingPlan['trainingPlan']['training_planId'].');">Pick This Meal Plan</button>
                                         </div>';
                                }
                                   
                            ?>
        </div>
    </div>
    <!-- Gallery Section End -->

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

    <script>
        function picktrainingPlan(trainingPlanId) {
            window.location.href = "trainingPlans.php?change=" + trainingPlanId;
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.set-bg').forEach(function(element) {
                var bg = element.getAttribute('data-setbg');
                element.style.backgroundImage = 'url(' + bg + ')';
            });
        });
    </script>
</body>

</html>