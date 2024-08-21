<?php
include 'connection.php';

if (!isset($_SESSION['adminUsername'])) {
    header('location:admin-login.php');
    exit();
}

// Fetch adminId
$adminUsername = $_SESSION['adminUsername'];
$adminQuery = "SELECT adminId FROM admin WHERE adminUsername = '$adminUsername'";
$adminResult = mysqli_query($conn, $adminQuery);
$adminRow = mysqli_fetch_assoc($adminResult);
$adminId = $adminRow['adminId'];

// Count unread messages
$unreadMessagesQuery = "SELECT COUNT(*) AS unreadCount FROM admin_messages WHERE adminId = '$adminId' AND readed = 0";
$unreadMessagesResult = mysqli_query($conn, $unreadMessagesQuery);
$unreadMessagesRow = mysqli_fetch_assoc($unreadMessagesResult);
$unreadCount = $unreadMessagesRow['unreadCount'];
?>

<!-- Offcanvas Menu Section Begin -->
<div class="offcanvas-menu-wrapper">
    <nav class="canvas-menu mobile-menu">
        <ul>
            <li class ="logo">
                <img src="img/logoo.png" alt="">
            </li>
            <li><a href="./admin-home.php">Home</a></li>
            <li><a href="./admin-memberships.php">Membership</a></li>
            <li><a href="./admin-gallery.php">Gallery</a></li>
            <li><a href="./admin-products.php">Products</a></li>
            <li><a href="./add-product.php">Add Product</a></li>
            <li><a href="./admin-orders.php">Store Orders</a></li>
            <li><a href="./trainers-edit.php">Trainers</a></li>
            <li><a href="./admin-trainees.php">Trainees</a></li>
            <li><a href="./add-trainer.php">Add Trainer</a></li>
            <li style="float:right"><a href="./logout.php">Logout</a></li>
            <li style="float:right">
                <a href="./admin-messages.php">
                    <i class="fa fa-envelope"></i> Messages
                    <?php if ($unreadCount > 0) { ?>
                        <span class="badge"><?php echo $unreadCount; ?></span>
                    <?php } ?>
                </a>
            </li>  
        </ul>
    </nav>
</div>

<style>
    .badge {
        background-color: red;
        color: white;
        padding: 3px 7px;
        border-radius: 50%;
        font-size: 12px;
        vertical-align: top;
        margin-left: 5px;
    }
</style>
