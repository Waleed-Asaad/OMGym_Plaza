<?php
include 'connection.php';
session_start();
// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT productId, productName, description, image,price FROM products";
$result = $conn->query($sql);

if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];
    $userId = $_SESSION['userId'];

    $existingQuery = "SELECT * FROM cart WHERE userId = '$userId' AND productId = '$productId'";
    $existingResult = mysqli_query($conn, $existingQuery);

    if ($existingResult->num_rows > 0) {
        echo "This product is already in the cart.";
    } 
	else {
        $productQuery = "SELECT * FROM products WHERE productId = '$productId'";
        $productResult = mysqli_query($conn, $productQuery);

        if ($productResult->num_rows > 0) {
            $item = $productResult->fetch_assoc();
            $productName = $item['productName'];
            $price = $item['price'];
            $quantity = 1;
			$productId=$item['productId'];

            $insertQuery = "INSERT INTO cart (userId, productId, productName, quantity, price) VALUES ('$userId', '$productId', '$productName', '$quantity', '$price')";
            mysqli_query($conn, $insertQuery);

            header('location: cart.php');
        }
    }
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
        .product-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>

<body>
<?php
    
    $user_email = $_SESSION['userEmail'];
   $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
   $result = mysqli_query($conn, $select); 
   $row = mysqli_fetch_array($result);
   if($row['status']=="trainee"){
    include 'traineeMenu.php';
   }
   else if($row['status']=="user"){
    include 'userMenu.php';
   }
   else{
    include 'trainer_menu.php';
   }
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>STORE</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <a href="#">Pages</a>
                            <span>Store</span>
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
                        <span>WELCOME TO OUR STORE</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="table-controls">
                        <ul>
                            <li class="active" data-tsfilter="all">All products</li>
                            <li data-tsfilter="Protein Powders">Protein Powders</li>
                            <li data-tsfilter="Protein Bars">Protein Bars</li>
                            <li data-tsfilter="Supplements">Supplements</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    
    </section>
    <section class="pricing-section spad">
    <div class="container">
        
        <div class="row justify-content-center">
            <?php
            if ($result->num_rows > 0) {
                // פלט של כל מוצר ככרטיס נפרד
                while ($row = $result->fetch_assoc()) { ?>
                    <div class="col-lg-4 col-md-8">
                        <div class="ps-item">
                            <h2 style="color:beige"><?php echo $row["productName"]; ?></h2>
                            <?php echo "<img class='product-image' src ='img/products/".$row['image']."'>";?>
                            <div class="pi-price">
                                <h2><?php echo "$" . $row["price"]; ?></h2>
                                <span><?php echo $row["description"]; ?></span>
                            </div>
                            <button class="primary-btn pricing-btn"><a href="store.php ? productId=<?php echo $row["productId"]; ?>">Order Now</a></button>
                        </div>
                    </div>
                <?php }
            } else {
                echo "<p>No products found.</p>";
            } ?>
        </div>
    </div>
</section>

    <!-- Class Timetable Section End -->

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