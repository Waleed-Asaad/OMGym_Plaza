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

    
</head>

<body>
<?php
    include 'traineeMenu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section style="height:2000px" class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
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

                                $sql = "SELECT t.planImage FROM trainee tr JOIN meal_plans t ON tr.meal_planId = t.meal_planId WHERE tr.userId = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $mealPlanImg = $row['planImage'];
                                
                                if($mealPlanImg){
                                    echo '<div style="width:800px;height:1300px;  margin-left:200px" class="gs-item grid-wide set-bg" data-setbg="img/meal_plans/'.$mealPlanImg.'">
                                    <a href="img/meal_plans/'.$mealPlanImg.'"  class="thumb-icon image-popup"><i  class="fa fa-picture-o"></i></a>
                                 </div>';
                                }
                                else{
                                    echo '<h2>YOU DID NOT PICK MEAL PLAN YET</h2>';
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

                                $sql = "SELECT * FROM meal_plans";
                                $result = mysqli_query($conn, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $score = 0;
                                    foreach ($attributes as $attribute) {
                                         if (strpos($goal, $attribute) !== false) {
                                            if ($row[$attribute] == 1) {
                                               $score++;
                                           }
                                        }
                                     }
                                      $mealPlans[] = ['mealPlan' => $row, 'score' => $score];
                                }

                                // Sort trainers by score in descending order
                                usort($mealPlans, function($a, $b) {
                                    return $b['score'] - $a['score'];
                                });

                                // Get the top 4 mealPlans
                                $top_mealPlans = array_slice($mealPlans, 0, 4);

                                // Display the top 4 mealPlans
                                
                                foreach ($top_mealPlans as $mealPlan) {
                                   $mealPlanImg = $mealPlan['mealPlan']['planImage'];
                                   echo '
                                        <div style="width:750px;height:1200px;margin-top:100px" class="gs-item grid-wide set-bg" data-setbg="img/meal_plans/'.$mealPlanImg.'">
                                         <a href="img/meal_plans/'.$mealPlanImg.'"  class="thumb-icon image-popup"><i style="width:2000px" class="fa fa-picture-o"></i></a>
                                         <p style="font-size:20px ; color:white">'.$mealPlan["score"].' Matches</p>
                                        <button style="padding: 0; width: 100%; background: #f36105; color: white" onclick="pickMealPlan('.$mealPlan['mealPlan']['meal_planId'].');">Pick This Meal Plan</button>
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
        function pickMealPlan(mealPlanId) {
            window.location.href = "mealPlans.php?change=" + mealPlanId;
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