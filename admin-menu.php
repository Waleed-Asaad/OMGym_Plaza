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
            <li class="logo">
                <img src="img/logoo.png" alt="">
            </li>
            <li><a href="./admin-home.php">Home</a></li>
            <li><a href="./admin-memberships.php">Membership</a></li>
            <li><a href="./admin-gallery.php">Gallery</a></li>
            <li>
                <a href="#">Products</a>
                <ul class="dropdown">
                    <li><a href="./admin-products.php">View Products</a></li>
                    <li><a href="./add-product.php">Add Product</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Trainers</a>
                <ul class="dropdown">
                    <li><a href="./trainers-edit.php">View Trainers</a></li>
                    <li><a href="./add-trainer.php">Add Trainer</a></li>
                </ul>
            </li>
            <li><a href="./admin-orders.php">Store Orders</a></li>
            <li><a href="./admin-trainees.php">Trainees</a></li>
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

    /* Dropdown styling */
    .dropdown {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 6px 14px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdown li {
        color: black;
        padding: 10px 14px;
        text-decoration: none;
        display: inline-block;
        text-align: left;
    }

    .dropdown li a {
        color: black;
    }

    .dropdown li:hover {
        background-color: #f1f1f1;
    }

    li:hover .dropdown {
        display: block;
    }
</style>
