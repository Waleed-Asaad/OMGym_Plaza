<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        <?php 
            include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; 
        ?>
        .trainers {
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
        <div class="trainers">
            <?php
                $sql = "SELECT * FROM trainer ORDER BY trainerId";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $trainerName = $row['trainerName'];
                        $trainerImg = $row['trainerImg'];
                        $rating = $row['rating'];
                        $muscle_building = $row['muscle_building'];
                        $weight_loss = $row['weight_loss'];
                        $strength = $row['strength'];
                        $raiting_avg = $row['rating'];
                        
                        echo "
                        <div class='card'>
                            <img src='img/team/$trainerImg' alt='$trainerName'>
                            <h3>$trainerName</h3>
                            <p class='price'>Rating: $rating/5.0</p>
                            <p>Roles:</p>";
                        
                        if ($muscle_building) {
                            echo "<p>Muscle Building</p>";
                        }
                        if ($weight_loss) {
                            echo "<p>Weight Loss</p>";
                        }
                        if ($strength) {
                            echo "<p>Strength</p>";
                        }
                        
                        echo "
                            <button type='submit' class='delete'>Delete</button>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No trainers found.</p>";
                }
            ?>
        </div>
    </div>
</body>
</html>
