<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

$query = "SELECT products.productName, products.image, SUM(productinorder.quantity) as total_sells 
          FROM products 
          JOIN productinorder ON products.productId = productinorder.productId 
          GROUP BY products.productId 
          ORDER BY total_sells DESC 
          LIMIT 5";
$result = mysqli_query($conn, $query);

$product_names = [];
$total_sells = [];
$product_images = [];

while($row = mysqli_fetch_assoc($result)){
    $product_names[] = $row['productName'];
    $total_sells[] = $row['total_sells'];
    $product_images[] = $row['image'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 5 Sold Products</title>
    <style>
        <?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'admin-menu.php'; ?>

<section class="top-products">
    <h2>Top 5 Sold Products</h2>
    <canvas id="topProductsChart"></canvas>
</section>

<script>
    var ctx = document.getElementById('topProductsChart').getContext('2d');
    var topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($product_names); ?>,
            datasets: [{
                label: 'Total Sells',
                data: <?php echo json_encode($total_sells); ?>,
                backgroundColor: 'hsla(120,100%,25%,0.3)',
                borderColor: 'hsla(160,100%,25%,0.3)',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: true,
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                            stepSize: 2
                    }
                }
            }
        }
    });
</script>

</body>
</html>
