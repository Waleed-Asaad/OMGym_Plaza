<?php
include 'connection.php';
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userEmail = $_SESSION['userEmail'];

// Determine user type and fetch userId
$user_query = "SELECT userId, status FROM user WHERE userEmail = '$userEmail'";
$user_result = mysqli_query($conn, $user_query);
$user_row = mysqli_fetch_assoc($user_result);
$userId = $user_row['userId'];
$userStatus = $user_row['status'];

if ($userStatus == "trainee") {
    $query = "SELECT userId FROM trainee WHERE userId = '$userId'";
} elseif ($userStatus == "trainer") {
    $query = "SELECT userId FROM trainer WHERE userId = '$userId'";
} else {
    $query = "SELECT userId FROM user WHERE userId = '$userId'";
}

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$userId = $row['userId'];

// Fetch purchase history
$history_query = "
    SELECT o.orderId, o.dateOfPurchase, o.total_price, p.productName, p.price, po.quantity, p.image
    FROM tborder o
    JOIN productinorder po ON o.orderId = po.orderId
    JOIN products p ON po.productId = p.productId
    WHERE po.userId = '$userId' AND o.status = 'completed'
    ORDER BY o.dateOfPurchase DESC";
$history_result = mysqli_query($conn, $history_query);

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Template">
    <meta name="keywords" content="Gym, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gym | Purchase History</title>

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
        .history-table{
            width: 950px;
        }
        td, th, h4 {
            color:white;
        }
        .tr-class{
            background: #f36100;
        }
        .product-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>

<body>
<?php
    if($user_row['status'] == "trainee"){
        include 'traineeMenu.php';
    } elseif($user_row['status'] == "user"){
        include 'userMenu.php';
    } else {
        include 'trainer_menu.php';
    }
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>PURCHASE HISTORY</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <span>Purchase History</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Purchase History Section Begin -->
    <section class="class-timetable-section spad">
        <div class="container">
            <div class="section-title">
                <span>Your Purchase History</span>
            </div>
            <?php if (mysqli_num_rows($history_result) > 0) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="history-table">
                            <?php
                            $current_order_id = null;
                            while ($row = mysqli_fetch_assoc($history_result)) {
                                if ($current_order_id != $row['orderId']) {
                                    if ($current_order_id != null) {
                                        echo '<tbody><table><div><div>';
                                    }
                                    $current_order_id = $row['orderId'];
                                    echo '<div class="order-item">
                                        <h4> Date: ' . $row['dateOfPurchase'] . ' | Total Price: $' . $row['total_price'] . '</h4>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr class="tr-class">
                                                    <th>Product Image</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                }
                                echo '<tr>
                                    <td style="width: 150px;"><img class="product-image" src="img/products/' . $row['image'] . '" alt="' . $row['productName'] . '"></td>
                                    <td>' . $row['productName'] . '</td>
                                    <td style="width: 60px;">' . $row['quantity'] . '</td>
                                    <td  style="width: 60px;">$' . $row['price'] . '</td>
                                    </tr>';
                            }
                            echo '</tbody>
                                    </table>
                                </div>
                            </div>';
                            ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <p>No purchase history found.</p>
            <?php } ?>
        </div>
    </section>
    <!-- Purchase History Section End -->

    <!-- Get In Touch Section Begin -->
    <?php include 'getInTouch.php'; ?>
    <!-- Get In Touch Section End -->

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

</body>

</html>
