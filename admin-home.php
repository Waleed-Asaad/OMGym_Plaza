<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}
$query = "SELECT p.productId, p.productName, p.image, SUM(po.quantity) as total_sold
          FROM products p
          JOIN productinorder po ON p.productId = po.productId
          GROUP BY p.productId, p.productName, p.image
          ORDER BY total_sold DESC
          LIMIT 5";

$result = mysqli_query($conn, $query);
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

    <div class="top-products">
    <h2>Top 5 Selling Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Image</th>
                <th>Total Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['productName']; ?></td>
                <td><img src="img/products/<?php echo $row['image']; ?>" alt="<?php echo $row['productName']; ?>" style="width:100px;"></td>
                <td><?php echo $row['total_sold']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>