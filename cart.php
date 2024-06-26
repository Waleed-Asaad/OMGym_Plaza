<?php
// session_start();

// if (!isset($_SESSION['cart'])) {
//     $_SESSION['cart'] = array();
// }

// // בדיקת חיבור ל-Database
// include 'connection.php';

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


// if (isset($_POST['add_to_cart'])) {
//     $product_id = $_POST['product_id'];
//     $quantity = $_POST['quantity'];

//     $sql = "SELECT * FROM products WHERE productId = $product_id";
//     $result = $conn->query($sql);

//     if ($result->num_rows > 0) {
//         $product = $result->fetch_assoc();

//         $cart_item = array(
//             'id' => $product['productId'],
//             'name' => $product['productName'],
//             'price' => $product['price'],
//             'quantity' => $quantity
//         );

//         array_push($_SESSION['cart'], $cart_item);
//     }
// }


// if (isset($_GET['action']) && $_GET['action'] == 'remove') {
//     $product_id = $_GET['id'];
//     foreach ($_SESSION['cart'] as $key => $item) {
//         if ($item['id'] == $product_id) {
//             unset($_SESSION['cart'][$key]);
//             break;
//         }
//     }
// }
?>
<?php

include 'connection.php';
session_start();




if(isset($_POST['update_cart'])){
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   $products_query = mysqli_query($conn, "SELECT * FROM products WHERE productId = (SELECT productId FROM cart WHERE id = '$update_id')");
   $fetch_product = mysqli_fetch_assoc($products_query);
   $quantity = $fetch_product['quantity'];
   if($update_quantity>$quantity){
	   $message[] = 'The quantity selected for '.$fetch_product['productName'].' is greater than the available quantity.';
   }
   else{
		mysqli_query($conn, "UPDATE cart SET quantity = '$update_quantity' WHERE id = '$update_id'");
		$message[] = 'cart quantity updated successfully!';
   }
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM cart WHERE id = '$remove_id'");
   header('location:cart.php');
}
  
if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM cart WHERE userId = '$user_id'");
   header('location:cart.php');
}

if(isset($_POST['checkout'])) {
   $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE userId = '$user_id'");
   $purchase_succeeded = false;
   
   while($fetch_cart = mysqli_fetch_assoc($cart_query)){
      $update_id = $fetch_cart['id'];
      $update_quantity = $fetch_cart['quantity'];
      $product_query = mysqli_query($conn, "SELECT quantity FROM products WHERE productId = (SELECT productId FROM cart WHERE id = '$update_id')");
	  $fetch_product = mysqli_fetch_assoc($product_query);
	  if($fetch_product==null){
		  $message[] = 'The '.$fetch_cart['productName'].' is out of stock';
	  } 
	  else{
			$available_quantity = $fetch_product['quantity'];
      
		if($update_quantity > $available_quantity){
			$message[] = 'The quantity selected for '.$fetch_cart['productName'].' is greater than the available quantity.';
		} 
		else {
			mysqli_query($conn, "UPDATE cart SET quantity = '$update_quantity' WHERE id = '$update_id'");
		 
			if($update_quantity == $available_quantity){
				$purchase_succeeded = true;
				$updated_quantity = $available_quantity - $update_quantity;
				mysqli_query($conn, "UPDATE products SET quantity = '$updated_quantity' WHERE productId = (SELECT productId FROM cart WHERE id = '$update_id')");
			}
			else {
				$updated_quantity = $available_quantity - $update_quantity;
				$purchase_succeeded = true;
				mysqli_query($conn, "UPDATE products SET quantity = '$updated_quantity' WHERE productId = (SELECT productId FROM cart WHERE id = '$update_id')");
			}
		}
	   }
    }
	if($purchase_succeeded){
		$message[] = 'Purchase succeeded!';
		mysqli_query($conn, "DELETE FROM cart WHERE userId = '$user_id'");
    }
	  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Shop">
    <meta name="keywords" content="Gym, Shop, HTML">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shopping Cart</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/cart.css" type="text/css">
</head>
<body>

<?php
    
    $user_email = $_SESSION['userEmail'];
   $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
   $result = mysqli_query($conn, $select); 
   $row = mysqli_fetch_array($result);
   if($row['status']=="trainee"){
    include 'traineeMenu.php';
   }
   else if($row['status']=="user"){
    include 'userMenu.php';
   }
   else if($row['status']=="trainer"){
      include 'trainer_menu.php';
     }
?>

<section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>Cart</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <a href="#">Pages</a>
                            <span>Cart</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<section class="cart-section spad">
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>

<div class="shopping-cart">
   <h1 class="heading">shopping cart</h1>
   <table>
      <thead>
         <th>model</th>
         <th>price</th>
         <th>quantity</th>
         <th>total price</th>
         <th>action</th>
      </thead>
      <tbody>
      <?php
         $user_email = $_SESSION['userEmail'];
         $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
         $result = mysqli_query($conn, $select); 
         $row = mysqli_fetch_array($result);
         $user_id = @row['userId'];
         $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE userId = '$user_id'");
         $grand_total = 0;
         if(mysqli_num_rows($cart_query) > 0){
            while($fetch_cart = mysqli_fetch_assoc($cart_query)){
      ?>
         <tr>
            <td><?php echo $fetch_cart['productName']; ?></td>
            <td>$<?php echo $fetch_cart['price']; ?></td>
            <td>
               <form action="" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                  <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                  <input type="submit" name="update_cart" value="update" class="edit1">
               </form>
            </td>
            <td>$<?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?></td>
            <td><a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('remove item from cart?');">remove</a></td>
         </tr>
      <?php
         $grand_total += $sub_total;
            }
         }else{
            echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no item added</td></tr>';
         }
      ?>
      <tr class="table-bottom">
         <td colspan="4">grand total :</td>
         <td>$<?php echo $grand_total; ?></td>
         <td><a href="cart.php?delete_all" onclick="return confirm('delete all from cart?');" class="delete1 <?php echo ($grand_total > 1)?'':'disabled'; ?>">Delete all</a></td>
      </tr>
   </tbody>
   </table>

   <form method="post" action="">  
		<p><a  class="formBtn1" href = "product.php">Continue Shopping </a></p><br>
		<button type="submit" name="checkout" class="formBtn2">Check out</button>
   </form>

</div>

</div>
</section>

<!-- Js Plugins -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/masonry.pkgd.min.js"></script>
<script src="js/jquery.barfiller.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
