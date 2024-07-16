<?php

include 'connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM membership";
$result = mysqli_query($conn, $sql);

$memberships = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $memberships[] = $row;
    }
}
?>

<section class="pricing-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <span>Our Plans</span>
                    <h2>Choose your pricing plan</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($memberships as $membership) { ?>
                <div class="col-lg-4 col-md-8">
                    <div class="ps-item">
                        <h3><?php echo $membership['name']; ?></h3>
                        <div class="pi-price">
                            <h2>$<?php echo $membership['price']; ?></h2>
                            <span><?php echo $membership['period']; ?> Month Subscription</span>
                        </div>
                        <ul>
                            <li>Free riding</li>
                            <li>Unlimited equipments</li>
                            <li>Personal trainer</li>
                            <li>Weight losing classes</li>
                            <li>No time restriction</li>
                        </ul>
                        <a href="user-login.php" class="primary-btn pricing-btn" <?php echo $membership['id']; ?>>Enroll now</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>