<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['adminUsername'])) {
    header('location:admin-login.php');
    exit();
}

// Fetch orders and related products, user information
$sql = "
    SELECT o.orderId, o.dateOfPurchase, o.total_price, o.status, o.statusUpdateDate, p.productName, p.price, po.quantity, p.image, u.userName, u.userAddress, u.userId
    FROM tborder o
    JOIN productinorder po ON o.orderId = po.orderId
    JOIN products p ON po.productId = p.productId
    JOIN user u ON po.userId = u.userId
    ORDER BY FIELD(o.status, 'pending approval', 'approved', 'shipped', 'cancelled', 'completed'), o.dateOfPurchase DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orderId'])) {
    $orderId = $_POST['orderId'];
    $status = $_POST['status'];

    // Update order status and status update date
    $updateSql = "UPDATE tborder SET status = '$status', statusUpdateDate = NOW() WHERE orderId = '$orderId'";

    if (mysqli_query($conn, $updateSql)) {
        // Add message to user
        $userId = $_POST['userId'];
        $messageContent = '';

        // Verify the switch case conditions
        $messagedate = date('d-m-Y H:i');
        switch ($status) {
            case 'approved':
                $messageContent = "In $messagedate, Order number: $orderId has been approved.";
                break;
            case 'shipped':
                $messageContent = "In $messagedate, The package from order number: $orderId is on its way to you.";
                break;
            case 'cancelled':
                $messageContent = "In $messagedate, Order number: $orderId has been cancelled, products must be selected again.";
                break;
        }

        // Ensure the message is created and inserted
        if ($messageContent != '') {
            $insertMessageSql = "INSERT INTO messages (content, userId) VALUES ('$messageContent', '$userId')";
            mysqli_query($conn, $insertMessageSql);
        }

        header('location:admin-orders.php');
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <style>
        <?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?>
        .order-container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-products {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-products img {
            max-width: 100px;
            height: auto;
        }

        .product-item {
            width: 30%;
            margin-bottom: 20px;
            text-align: center;
        }

        .order-details {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .order-status {
            margin-top: 10px;
            text-align: right;
        }

        .order-status form select {
            padding: 5px;
            font-size: 14px;
        }

        .order-status form button {
            padding: 5px 10px;
            margin-left: 10px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .order-status form button:hover {
            background-color: #0056b3;
        }

        .pending-approval {
            background-color: #fff3e0; /* רקע כתום בהיר מאוד */
        }

        .shipped {
            background-color: #e3f2fd; /* רקע כחול בהיר מאוד */
        }

        .approved {
            background-color: #d4edda; /* רקע ירוק בהיר מאוד */
        }
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>

    <div class="orders-section">
        <h1 style="text-align:center; margin:20px 10px;">Manage Orders</h1>

        <?php if (mysqli_num_rows($result) > 0) { 
            $currentOrderId = null;
        ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $rowClass = '';
                if ($row['status'] == 'pending approval') {
                    $rowClass = 'pending-approval';
                } elseif ($row['status'] == 'shipped') {
                    $rowClass = 'shipped';
                } elseif ($row['status'] == 'approved') {
                    $rowClass = 'approved';
                }

                if ($currentOrderId !== $row['orderId']) {
                    if ($currentOrderId !== null) {
                        echo '</div><div class="order-details">';
                        echo '<p>Name: ' . $lastRow['userName'] . '  |  Address: ' . $lastRow['userAddress'] . '</p>';
                        echo '</div><div class="order-status">';
                        echo '<p>Status: ' . $lastRow['status'] . '</p>';
                        echo '<p>Status Last Updated: ' . $lastRow['statusUpdateDate'] . '</p>';
                        
                        if ($lastRow['status'] != 'completed') { 
                            echo '<form method="post" action=""><input type="hidden" name="orderId" value="' . $lastRow['orderId'] . '">';
                            echo '<input type="hidden" name="userId" value="' . $lastRow['userId'] . '">';
                            echo '<select name="status">
                                    <option value="pending approval" ' . ($lastRow['status'] == 'pending approval' ? 'selected' : '') . '>Pending Approval</option>
                                    <option value="approved" ' . ($lastRow['status'] == 'approved' ? 'selected' : '') . '>Approved</option>
                                    <option value="shipped" ' . ($lastRow['status'] == 'shipped' ? 'selected' : '') . '>Shipped</option>
                                    <option value="cancelled" ' . ($lastRow['status'] == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                                </select>';
                            echo '<button type="submit">Update Status</button>';
                            echo '</form>';
                        }
                        echo '</div></div>';
                    }
                    $currentOrderId = $row['orderId'];
                    ?>
                    <div class="order-container <?php echo $rowClass; ?>">
                        <div class="order-header">
                            <h2>Order #<?php echo $row['orderId'];?></h2>
                            <span>Order Date: <?php echo $row['dateOfPurchase']; ?></span>
                            <span>Total: $<?php echo $row['total_price']; ?></span>
                        </div>
                        <div class="order-products">
                    <?php }
                    $lastRow = $row;
                    ?>
                        <div class="product-item">
                            <img src="img/products/<?php echo $row['image']; ?>" alt="<?php echo $row['productName']; ?>">
                            <p><?php echo $row['productName']; ?></p>
                            <p>Quantity: <?php echo $row['quantity']; ?></p>
                            <p>Price: $<?php echo $row['price']; ?></p>
                        </div>
            <?php } // End while loop 
            if ($currentOrderId !== null) {
                echo '</div><div class="order-details">';
                echo '<p>User: ' . $lastRow['userName'] . '</p>';
                echo '<p>Address: ' . $lastRow['userAddress'] . '</p>';
                echo '</div><div class="order-status">';
                echo '<p>Status: ' . $lastRow['status'] . '</p>';
                echo '<p>Status Last Updated: ' . $lastRow['statusUpdateDate'] . '</p>';
                
                if ($lastRow['status'] != 'completed') { 
                    echo '<form method="post" action=""><input type="hidden" name="orderId" value="' . $lastRow['orderId'] . '">';
                    echo '<input type="hidden" name="userId" value="' . $lastRow['userId'] . '">';
                    echo '<select name="status">
                            <option value="pending approval" ' . ($lastRow['status'] == 'pending approval' ? 'selected' : '') . '>Pending Approval</option>
                            <option value="approved" ' . ($lastRow['status'] == 'approved' ? 'selected' : '') . '>Approved</option>
                            <option value="shipped" ' . ($lastRow['status'] == 'shipped' ? 'selected' : '') . '>Shipped</option>
                            <option value="cancelled" ' . ($lastRow['status'] == 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                        </select>';
                    echo '<button type="submit">Update Status</button>';
                    echo '</form>';
                }
                echo '</div></div>';
            }
            ?>
        <?php } else { ?>
            <p>No orders found.</p>
        <?php } ?>
    </div>

</body>
</html>
