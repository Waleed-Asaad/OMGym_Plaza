<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

// שאילתה למוצרים הנמכרים ביותר
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

// שאילתה למאמנים בעלי הדירוג הגבוה ביותר וכמות המדרגים הגבוהה ביותר
$trainer_query = "SELECT trainerName, rating, raiting_counter 
                  FROM trainer 
                  ORDER BY rating DESC, raiting_counter DESC 
                  LIMIT 3";
$trainer_result = mysqli_query($conn, $trainer_query);

$trainer_names = [];
$trainer_ratings = [];
$trainer_counters = [];

while($row = mysqli_fetch_assoc($trainer_result)){
    $trainer_names[] = $row['trainerName'];
    $trainer_ratings[] = $row['rating'];
    $trainer_counters[] = $row['raiting_counter'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 5 Sold Products & Top Trainers</title>
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

<section class="top-trainers">
    <h2>Top 3 Trainers by Rating</h2>
    <canvas id="topTrainersChart"></canvas>
</section>

<script>
    var ctxProducts = document.getElementById('topProductsChart').getContext('2d');
    var topProductsChart = new Chart(ctxProducts, {
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

    var ctxTrainers = document.getElementById('topTrainersChart').getContext('2d');
    var topTrainersChart = new Chart(ctxTrainers, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($trainer_names); ?>,
            datasets: [{
                label: 'Rating',
                data: <?php echo json_encode($trainer_ratings); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Rating Counter',
                data: <?php echo json_encode($trainer_counters); ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
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
                        stepSize: 1
                    }
                }
            }
        }
    });

    // הדפסת הנתונים כדי לוודא שהם מועברים כראוי
    console.log("Trainer Names:", <?php echo json_encode($trainer_names); ?>);
    console.log("Trainer Ratings:", <?php echo json_encode($trainer_ratings); ?>);
    console.log("Trainer Counters:", <?php echo json_encode($trainer_counters); ?>);
</script>

</body>
</html>
