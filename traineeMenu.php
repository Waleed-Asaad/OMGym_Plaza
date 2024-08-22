<?php 
include "connection.php";

// Retrieve the email from the session
$email = $_SESSION['userEmail'];

// Fetch the userId based on the user email
$sql = "SELECT userId FROM user WHERE userEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$userId = $row['userId'];

$sql = "SELECT traineeId FROM trainee WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$traineeId = $row['traineeId'];

// Count unread messages for the trainee
function getUnreadMessagesCount($traineeId, $conn) {
    $sql = "SELECT COUNT(*) as unreadCount FROM messages WHERE traineeId = ? AND readed = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $traineeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['unreadCount'];
}

// Get the number of unread messages
$unreadCount = getUnreadMessagesCount($traineeId, $conn);
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
                <li><a href="./traineeHome.php">Trainee Home</a></li>
                <li><a href="#">Store</a>
                    <ul class="dropdown">
                        <li><a href="./store.php">Our Store</a></li>
                        <li><a href="./cart.php">Cart <i class="fas fa-shopping-cart"></i></a></li>
                        <li><a href="./orders.php">Orders</a></li>
                        <li><a href="./history.php">History</a></li>
                    </ul>
                </li>
                <li><a href="./trainers.php">Trainers</a></li>
                <li><a href="./mealPlans.php">Meal Plans</a></li>
                <li><a href="./traineeTrainerSchedule.php">Trainer Schedule</a></li>
                <li><a href="./myClasses.php">My Classes</a></li>
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
                            <li><a href="./traineeHome.php">Trainee Home</a></li>
                            <li><a href="#">Store</a>
                                <ul class="dropdown">
                                    <li><a href="./store.php">Our Store</a></li>
                                    <li><a href="./cart.php">Cart <i class="fas fa-shopping-cart"></i></a></li>
                                    <li><a href="./orders.php">Orders</a></li>
                                    <li><a href="./history.php">History</a></li>
                                </ul>
                            </li>
                            <li><a href="./trainers.php">Trainers</a></li>
                            <li><a href="./mealPlans.php">Meal Plans</a></li>
                            <li><a href="./traineeTrainerSchedule.php">Trainer Schedule</a></li>
                            <li><a href="./myClasses.php">My Classes</a></li>
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

    <!-- JavaScript for Popup -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Similar to user implementation, you can add the popup functionality here.
    </script>
</body>
</html>
