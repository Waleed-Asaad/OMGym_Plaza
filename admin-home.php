<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

if (isset($_POST['delete'])) {
    $productId = $_POST['delete'];

    $deleteSql = "DELETE FROM products WHERE productId = $productId";
    mysqli_query($conn, $deleteSql);
	header("Location: admin-home.php");
}

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