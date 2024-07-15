<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        <?php 
            include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; 
        ?>
        .trainees {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card img {
            width: 100%;
            height: auto;
        }
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <div class="container">
        <div class="trainees">
            <?php
                $sql = "SELECT t.traineeId, t.traineeName, t.traineeImg, 
                               t.startingMembership,
                               DATE_FORMAT(t.startingMembership, '%d-%m-%Y') as subscriptionDate, 
                               m.period 
                        FROM trainee t
                        JOIN membership m ON t.membershipId = m.id
                        ORDER BY t.traineeId";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $traineeId = $row['traineeId'];
                        $traineeName = $row['traineeName'];
                        $traineeImg = $row['traineeImg'];
                        $startingMembership = $row['startingMembership'];
                        $subscriptionDate = $row['subscriptionDate'];
                        $period = $row['period'];
                        
                        // חישוב הימים שנותרו למנוי
                        $startingDate = new DateTime($startingMembership);
                        $endingDate = clone $startingDate;
                        $endingDate->modify("+$period months");
                        $currentDate = new DateTime();
                        $remainingDays = $currentDate > $endingDate ? 0 : $currentDate->diff($endingDate)->days;
                        
                        echo "
                        <div class='card'>
                            <img src='img/trainees/$traineeImg' alt='$traineeName'>
                            <h3>$traineeName</h3>
                            <h5>Subscription Date: $subscriptionDate</h5>
                            <h5>Days Remaining: $remainingDays</h5>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No trainees found.</p>";
                }
            ?>
        </div>
    </div>
</body>
</html>
