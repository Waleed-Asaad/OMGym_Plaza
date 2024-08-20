<?php
include 'connection.php';
session_start();

$user_email = $_SESSION['userEmail'];
// שליפת userId לפי המייל
$sql = "SELECT userId, status FROM user WHERE userEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$user_id = $user_row['userId'];

// עדכון סטטוס להזמנה שהושלמה
if (isset($_POST['complete_order'])) {
    $order_id = $_POST['order_id'];
    $update_status_sql = "UPDATE tborder SET status = 'completed' WHERE orderId = ?";
    $stmt = $conn->prepare($update_status_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
}

// שליפת ההזמנות שלא הושלמו
$order_query = "
    SELECT o.orderId, o.dateOfPurchase, o.total_price, p.productName, p.price, po.quantity, p.image, o.status
    FROM tborder o
    JOIN productinorder po ON o.orderId = po.orderId
    JOIN products p ON po.productId = p.productId
    WHERE po.userId = ? AND o.status != 'completed'
    ORDER BY o.dateOfPurchase DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Template">
    <meta name="keywords" content="Gym, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gym | Purchase Orders</title>

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
        .status-icon {
            width: 30px;
            height: 30px;
        }
        .complete-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .complete-btn:hover {
            background-color: #218838;
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
                        <h2>PURCHASE ORDERS</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <span>Purchase Orders</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Purchase Orders Section Begin -->
    <section class="class-timetable-section spad">
        <div class="container">
            <div class="section-title">
                <span>Your Purchase Orders</span>
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
                                        echo '</tbody><table><div><div>';
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
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                }
                                $status_icon = "";
                                switch ($row['status']) {
                                    case "pending approval":
                                        $status_icon = "img/icons/pending.png";
                                        break;
                                    case "approved":
                                        $status_icon = "img/icons/approved.png";
                                        break;
                                    case "shipped":
                                        $status_icon = "img/icons/shipped.png";
                                        break;
                                    case "in transit":
                                        $status_icon = "img/icons/in_transit.png";
                                        break;
                                    default:
                                        $status_icon = "img/icons/unknown.png";
                                        break;
                                }
                                echo '<tr>
                                    <td style="width: 150px;"><img class="product-image" src="img/products/' . $row['image'] . '" alt="' . $row['productName'] . '"></td>
                                    <td>' . $row['productName'] . '</td>
                                    <td style="width: 60px;">' . $row['quantity'] . '</td>
                                    <td  style="width: 60px;">$' . $row['price'] . '</td>
                                    <td style="width: 100px;"><img class="status-icon" src="' . $status_icon . '" alt="' . $row['status'] . '"></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="order_id" value="' . $row['orderId'] . '">
                                            <button type="submit" name="complete_order" class="complete-btn">Mark as Completed</button>
                                        </form>
                                    </td>
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
                <p>No pending orders found.</p>
            <?php } ?>
        </div>
    </section>
    <!-- Purchase Orders Section End -->

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
