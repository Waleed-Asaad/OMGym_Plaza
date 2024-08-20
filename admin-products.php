<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminUsername'])){
    header('location:admin-login.php');
 }

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

//מחיקת מוצר
if (isset($_POST['delete'])) {
    $productId = $_POST['delete'];

    $deleteSql = "DELETE FROM products WHERE productId = $productId";
    mysqli_query($conn, $deleteSql);
    header("Location: admin-home.php");
}

//לעבור לדף עדכון מוצר
if (isset($_POST['edit'])) {
    $productId = $_POST['edit'];
    header("Location: admin-edit.php?productId=$productId");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style><?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?></style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin page</title>

    <style>
        .warning {
            background-color: #f36100;
            color: white;
            padding: 5px;
            margin-top: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .out-of-stock {
            background-color: red;
            color: white;
            padding: 5px;
            margin-top: 10px;
            text-align: center;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="product-container">
            <div class="product">
                <?php $productImg = $row['image']; echo "<img src ='img/products/$productImg'".$row['image']."'>";?>
                <h2><?php echo $row["productName"]; ?></h2>
                <p><?php echo $row["description"]; ?></p>
                <p>Price: $<?php echo $row["price"]; ?></p>
                <p>Quantity: <?php echo $row["quantity"]; ?></p>
                <?php if ($row["quantity"] > 0 && $row["quantity"] < 5) { ?>
                    <div class="warning">The Quantity is Running Out !</div>
                <?php } elseif ($row["quantity"] == 0) { ?>
                    <div class="out-of-stock">Out Of Stock !</div>
                <?php } ?>
                <div class="product-actions">
                    <form method="post" action="">
                        <input type="hidden" name="delete" value="<?php echo $row["productId"]; ?>">
                        <button type="submit" class="delete">Delete</button>
                    </form>
                    <form method="post" action="">
                        <input type="hidden" name="edit" value="<?php echo $row["productId"]; ?>">
                        <button type="submit" class="edit">Edit</button>
                    </form>
                </div>
            </div>
            </div>
            <?php
        }
    } else {
        echo "No products found.";
    }
    ?>
</body>
</html>
