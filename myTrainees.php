<?php

include 'connection.php';
session_start();

$user_id = $_SESSION['userId'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
        <?php 
            include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; 
        ?>
        .trainers {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            /* margin-top:400px; */
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



    <section style="height:1500px" class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div  class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                    
    
        <div class="trainers">
            <?php
                $sql = "SELECT * FROM trainee ORDER BY traineeId";
                $result = mysqli_query($conn, $sql);
                
                

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        
                        $trainee_id = $row['traineeId'];
                        $trainee_name = $row['traineeName'];
                        $trainee_img = $row['traineeImg'];
                        
                        echo "
                            <a href='myTrainee.php?trainee_id=$trainee_id' >
                            <div class='card'>
                            <img src='img/trainees/$trainee_img' alt='$trainee_name'>
                            <h3>$trainee_name</h3>
                            
                            </div>
                            </a>
                        ";
                    }
                } else {
                  echo "<p>No trainers found.</p>";
                }?>
                
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
</body>
</html>

