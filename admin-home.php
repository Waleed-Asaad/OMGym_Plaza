<?php
include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

$selected_year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

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

// שאילתה למאמנים בעלי הדירוג הנמוך ביותר וכמות המדרגים הגדולה ביותר
$trainer_query = "SELECT trainerName, rating 
                  FROM trainer 
                  WHERE raiting_counter > 0
                  ORDER BY rating ASC 
                  LIMIT 3";
$trainer_result = mysqli_query($conn, $trainer_query);

$trainer_names = [];
$trainer_ratings = [];

while($row = mysqli_fetch_assoc($trainer_result)){
    $trainer_names[] = $row['trainerName'];
    $trainer_ratings[] = $row['rating'];
}

// שאילתה למספר המתאמנים שקנו מנוי בכל חודש
$membership_query = "SELECT MONTH(startingMembership) as month, COUNT(*) as count 
                     FROM trainee 
                     WHERE YEAR(startingMembership) = $selected_year
                     GROUP BY MONTH(startingMembership)";
$membership_result = mysqli_query($conn, $membership_query);

$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
$membership_counts = array_fill(0, 12, 0); // לאפס את המערך ל-12 חודשים

while($row = mysqli_fetch_assoc($membership_result)){
    $membership_counts[$row['month'] - 1] = $row['count']; // מיקומים במערך הם מ-0 עד 11
}

$current_year = date("Y");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

<section class="bottom-trainers">
    <h2>Bottom 3 Trainers by Rating</h2>
    <canvas id="bottomTrainersChart"></canvas>
</section>

<section class="membership-by-month">
    <h2>Monthly Membership Purchases in <span id="selected-year"><?php echo $selected_year; ?></span></h2>
    <form id="year-form" method="get" action="">
        <label for="year">Select Year:</label>
        <select id="year" name="year" onchange="document.getElementById('year-form').submit();">
            <?php 
            for ($year = 2023; $year <= $current_year; $year++) {
                echo "<option value='$year'" . ($year == $selected_year ? " selected" : "") . ">$year</option>";
            }
            ?>
        </select>
    </form>
    <canvas id="membershipChart"></canvas>
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

    var ctxTrainers = document.getElementById('bottomTrainersChart').getContext('2d');
    var bottomTrainersChart = new Chart(ctxTrainers, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($trainer_names); ?>,
            datasets: [{
                label: 'Rating',
                data: <?php echo json_encode($trainer_ratings); ?>,
                backgroundColor: 'rgba(251, 90, 50, 0.6)',
                borderColor: 'rgba(251, 90, 50, 0.6)',
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

    var ctxMembership = document.getElementById('membershipChart').getContext('2d');
    var membershipChart = new Chart(ctxMembership, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Membership Purchases',
                data: <?php echo json_encode($membership_counts); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: true
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
</script>

</body>
</html>
