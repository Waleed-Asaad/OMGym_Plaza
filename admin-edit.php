<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['adminUsername'])) {
    header('location:admin-login.php');
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productName = '';
$quantity = '';
$description = '';
$price = '';

if (isset($_GET['productId'])) {
    $productId = $_GET['productId'];

    $sql = "SELECT * FROM products WHERE productId = $productId";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $productName = $row['productName'];
        $quantity = $row['quantity'];
        $description = $row['description'];
        $price = $row['price'];
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid request.";
}

if (isset($_POST['submit'])) {
    $productId = $_POST['productId'];
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $quantity = $_POST['quantity'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];

    $updateSql = "UPDATE products SET productName = '$productName', quantity = $quantity, description = '$description', price = $price WHERE productId = $productId";

    if (mysqli_query($conn, $updateSql)) {
        header("Location: admin-products.php");
        exit;
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        <?php 
            include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; 
            include 'C:\wamp64\www\omgym_plaza\css\edit.css';
        ?>
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <div class="add-form">
        <h1>Edit Product</h1>
        <form method="post" action="">
            <input type="hidden" name="productId" value="<?php echo $productId; ?>">
            <label for="model">Model:</label>
            <input type="text" name="productName" value="<?php echo htmlspecialchars($productName); ?>"><br>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>"><br>
            <label for="description">Description:</label>
            <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea><br>
            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($price); ?>"><br>
            <input type="submit" name="submit" value="Save">
        </form>
    </div>
</body>
</html>
