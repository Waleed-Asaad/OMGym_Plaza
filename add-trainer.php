
<?php

include 'connection.php';

session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

// $msg = "";
// if(isset($_POST['submit'])){
	
// 	//$target = "images/".basename($_FILES['image']['name']);
// 	//$productId=$_POST['productId'];
// 	$productName=$_POST['productName'];
// 	$quantity=$_POST['quantity'];
// 	$description=$_POST['description'];
// 	$price=$_POST['price'];
// 	//$image = $_FILES['image']['name'];
	
// 	$sql = "INSERT INTO products (productName,quantity,description,price) VALUES ('$productName','$quantity','$description','$price')";
// 	mysqli_query($conn,$sql);
	
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<style><?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?></style>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Trainer</title>

</head>
<body>
<?php include 'admin-menu.php'; ?>
<div class="form-container">
   <form method="post" action="" enctype="multipart/form-data">
      <h3>Add a new trainer</h3>
      <input type="text" name="productName" placeholder="Enter trainer name" required>
	  <input type="text" name="specialty" placeholder="Enter trainer specialty" required>
	  <!-- <input type="file" name="image" value="Upload Image"accept="image/png, image/jpg, image/jpeg" required> -->
	  <input type="submit" name="submit" value="Add" class="form-btn" required>
   </form>

</div>

</body>
</html>