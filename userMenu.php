<?php
include 'connection.php';

// הנח שהמייל שמור ב-$_SESSION['userEmail']
$email = $_SESSION['userEmail'];

// שליפת userId לפי המייל
$sql = "SELECT userId FROM user WHERE userEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$userId = $row['userId'];

// פונקציה לספירת ההודעות שלא נקראו
function getUnreadMessagesCount($userId, $conn) {
    $sql = "SELECT COUNT(*) as unreadCount FROM messages WHERE userId = ? AND readed = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['unreadCount'];
}

// ספירת ההודעות שלא נקראו
$unreadCount = getUnreadMessagesCount($userId, $conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .badge {
            background-color: red;
            color: white;
            padding: 2px 6px;
            border-radius: 50%;
            font-size: 12px;
            vertical-align: top;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <!-- Offcanvas Menu Section Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="canvas-close">
            <i class="fa fa-close"></i>
        </div>
        <div class="canvas-search search-switch">
            <i class="fa fa-search"></i>
        </div>
        <nav class="canvas-menu mobile-menu">
            <ul>
                <li><a href="./user-home.php">User Home</a></li>
                <li><a href="#">Store</a>
                    <ul class="dropdown">
                        <li><a href="./store.php">Our Store</a></li>
                        <li><a href="./orders.php">Orders</a></li>
                        <li><a href="./history.php">History</a></li>
                    </ul>
                </li>
                <li><a href="./subscription.php">Subscription</a></li>
                <li><a href="./cart.php">Cart <i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="./messages.php">Messages <i class="fas fa-envelope"></i>
                    <?php if ($unreadCount > 0) { ?>
                        <span class="badge"><?php echo $unreadCount; ?></span>
                    <?php } ?>
                </a></li>
                <li><a href="./logout.php">Logout <i class="fas fa-sign-out-alt"></i></a></li>  
            </ul>
        </nav>
    </div>

    <!-- Header Section Begin -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="./index.php">
                            <img src="img/logoo.png" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <nav class="nav-menu">
                        <ul>
                            <li><a href="./user-home.php">User Home</a></li>
                            <li><a href="#">Store</a>
                                <ul class="dropdown">
                                    <li><a href="./store.php">Our Store</a></li>
                                    <li><a href="./orders.php">Orders</a></li>
                                    <li><a href="./history.php">History</a></li>
                                </ul>
                            </li>
                            <li><a href="./subscription.php">Subscription</a></li>
                            <li><a href="./cart.php">Cart <i class="fas fa-shopping-cart"></i></a></li>
                            <li><a href="./messages.php">Messages <i class="fas fa-envelope"></i>
                                <?php if ($unreadCount > 0) { ?>
                                    <span class="badge"><?php echo $unreadCount; ?></span>
                                <?php } ?>
                            </a></li>
                            <li><a href="./logout.php">Logout <i class="fas fa-sign-out-alt"></i></a></li>
                        </ul>
                    </nav>
                </div>
                
            </div>
            <div class="canvas-open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
</body>
</html>
