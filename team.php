<?php 
@include "connection.php";
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
    include 'menu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Our Team</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <span>Our team</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Team Section Begin -->
    <section class="team-section team-page spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="team-title">
                        <div class="section-title">
                            <span>Our Team</span>
                            <h2>TRAIN WITH EXPERTS</h2>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="row">
            <?php
                $sql = "SELECT * FROM trainer";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $trainerName = $row['trainerName'];
                        $trainerImg = $row['trainerImg'];
                        $rating = $row['rating'];
                        $muscle_building = $row['muscle_building'];
                        $weight_loss = $row['weight_loss'];
                        $strength = $row['strength'];
                        $raiting_avg = $row['rating'];
                        echo "
                <div class='col-lg-4 col-sm-6'>
                    <div class='ts-item set-bg' data-setbg='img/team/$trainerImg'>
                        <div class='ts_text'>
                            <h4>$trainerName</h4>";
                                if ($muscle_building) {
                                    echo "<span>Muscle Building trainer</span>";
                                }
                                if ($weight_loss) {
                                    echo "<span>Weight Loss trainer</span>";
                                }
                                if ($strength) {
                                    echo "<span>Strength trainer</span>";
                                }
                            
                            echo "<span class='price'>Rating: $rating/5.0</span>";
                            echo "
                        </div>
                    </div>
                </div>";
            }
                } else {
                    echo '<p>No trainers found.</p>';
                }
            ?>
            </div>
        </div>
    </section>
    <!-- Team Section End -->

    <!-- Get In Touch Section Begin -->
    <?php 
        include 'getInTouch.php';
    ?>
    <!-- Get In Touch Section End -->

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


</body>

</html>