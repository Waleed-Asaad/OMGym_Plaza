<?php
include 'connection.php';
session_start();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

// המלצות על מוצרים דומים
$userEmail = $_SESSION['userEmail'];
$user_query = "SELECT userId FROM user WHERE userEmail = '$userEmail'";
$user_result = mysqli_query($conn, $user_query);
$user_row = mysqli_fetch_assoc($user_result);
$userId = $user_row['userId'];

// בדיקת הקנייה האחרונה של המשתמש
$last_order_query = "
    SELECT p.productId, p.category
    FROM productinorder po
    JOIN tborder o ON po.orderId = o.orderId
    JOIN products p ON po.productId = p.productId
    WHERE po.userId = '$userId'
    ORDER BY o.dateOfPurchase DESC
    LIMIT 3";
$last_order_result = mysqli_query($conn, $last_order_query);
$last_order_products = [];
$last_order_categories = [];

if (mysqli_num_rows($last_order_result) > 0) {
    while ($row = mysqli_fetch_assoc($last_order_result)) {
        $last_order_products[] = $row['productId'];
        $last_order_categories[] = $row['category'];
    }
}

$recommended_products = [];
$recommended_categories = [];

if (count($last_order_products) > 0) {
    // אם הייתה קנייה אחרונה, מוצרים שלא נרכשו מקטגוריות שונות
    $recommended_products_query = "
        SELECT *
        FROM products
        WHERE category NOT IN ('" . implode("','", $last_order_categories) . "')
        AND productId NOT IN (" . implode(",", $last_order_products) . ")
        GROUP BY category
        LIMIT 3";
    $recommended_products_result = mysqli_query($conn, $recommended_products_query);

    if (mysqli_num_rows($recommended_products_result) > 0) {
        while ($product_row = mysqli_fetch_assoc($recommended_products_result)) {
            $recommended_products[] = $product_row;
            $recommended_categories[] = $product_row['category'];
        }
    }
}

// אם לא מצאנו שלושה מוצרים מקטגוריות שונות, נוסיף מוצרים רנדומליים מקטגוריות שונות
if (count($recommended_products) < 3) {
    $remaining_count = 3 - count($recommended_products);
    $random_products_query = "
        SELECT *
        FROM products
        WHERE category NOT IN ('" . implode("','", $recommended_categories) . "')
        AND productId NOT IN (" . implode(",", $last_order_products) . ")
        GROUP BY category
        ORDER BY RAND()
        LIMIT $remaining_count";
    $random_products_result = mysqli_query($conn, $random_products_query);

    while ($product_row = mysqli_fetch_assoc($random_products_result)) {
        $recommended_products[] = $product_row;
        $recommended_categories[] = $product_row['category'];
    }
}

if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];
    $userEmail = $_SESSION['userEmail'];
    $user_id = "SELECT userId FROM user WHERE userEmail = '$userEmail'";
    $existingQuery = "SELECT * FROM cart WHERE userId = '$userId' AND productId = '$productId'";
    $existingResult = mysqli_query($conn, $existingQuery);
    $user_result = mysqli_query($conn, $user_id);
    $user = mysqli_fetch_assoc($user_result);
    $userId = $user['userId'];
    if ($existingResult->num_rows > 0) {
        echo "This product is already in the cart.";
    } else {
        $productQuery = "SELECT * FROM products WHERE productId = '$productId'";
        $productResult = mysqli_query($conn, $productQuery);

        if ($productResult->num_rows > 0) {
            $item = $productResult->fetch_assoc();
            $productName = $item['productName'];
            $image = $item['image'];
            $price = $item['price'];
            $quantity = 1;
            $productId = $item['productId'];

            $insertQuery = "INSERT INTO cart (userId, productId, productName, image, quantity, price) VALUES ('$userId', '$productId', '$productName', '$image', '$quantity', '$price')";
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
            width: 180px;
            height: 230px;
            object-fit: cover;
        }
    </style>
</head>

<body>
<?php
    
    $user_email = $_SESSION['userEmail'];
   $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
   $result1 = mysqli_query($conn, $select); 
   $row1 = mysqli_fetch_array($result1);
   if($row1['status']=="trainee"){
    include 'traineeMenu.php';
   }
   else if($row1['status']=="user"){
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
            </div>
        </div>
    </section>
    
    <!-- Recommended Products Section Begin -->
    <section class="pricing-section ">
        <div class="container">
        <h3 style="color: #ffffff;">Recommended for You</h3>
            <div class="row justify-content-center">
                <?php
                if (count($recommended_products) > 0) {
                    foreach ($recommended_products as $product) { ?>
                        <div class="col-lg-4 col-md-8">
                            <div class="ps-item">
                                <h2 style="color:beige"><?php echo $product["productName"]; ?></h2>
                                <?php echo "<img class='product-image' src ='img/products/".$product['image']."'>";?>
                                <div class="pi-price">
                                    <h2><?php echo "$" . $product["price"]; ?></h2>
                                    <span><?php echo $product["description"]; ?></span>
                                </div>
                                <button class="primary-btn pricing-btn"><a href="store.php?productId=<?php echo $product["productId"]; ?>">Add to Cart</a></button>
                            </div>
                        </div>
                    <?php }
                } else {
                    echo "<p>No recommended products found.</p>";
                } ?>
            </div>
        </div>
    </section>
    <!-- Recommended Products Section End -->
    
    <!-- All Products Section Begin -->
    <section class="pricing-section spad">
        <div class="container">
        <h3 style="color: #ec5606; padding: 20px;">* All Products *</h3>
            <div class="row justify-content-center">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <div class="col-lg-4 col-md-8">
                            <div class="ps-item">
                                <h2 style="color:beige"><?php echo $row["productName"]; ?></h2>
                                <?php echo "<img class='product-image' src ='img/products/".$row['image']."'>";?>
                                <div class="pi-price">
                                    <h2><?php echo "$" . $row["price"]; ?></h2>
                                    <span><?php echo $row["description"]; ?></span>
                                </div>
                                <button class="primary-btn pricing-btn"><a href="store.php?productId=<?php echo $row["productId"]; ?>">Add to Cart</a></button>
                            </div>
                        </div>
                    <?php }
                } else {
                    echo "<p>No products found.</p>";
                } ?>
            </div>
        </div>
    </section>
    <!-- All Products Section End -->

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
