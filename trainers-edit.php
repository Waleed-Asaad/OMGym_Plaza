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
	<style><?php 
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
   <title>admin page</title>

</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <div class="container">
        <div class="trainers">
            <div class="card">
                <img src="img\team\team-3.jpg" alt="team-3">
                <h3>Trainer 1</h3>
                <p class="price">Rating: 4.5/5.0</p>
                <p>Role: Fitness Trainer</p>
                <button type="submit" class="delete">Delete</button>
            </div>
            <div class="card">
                <img src="img\team\team-3.jpg" alt="team-3">
                <h3>Trainer 2</h3>
                <p class="price">Rating: 4.7/5.0</p>
                <p>Role: Fitness Trainer</p>
                <button type="submit" class="delete">Delete</button>
            </div>
            <div class="card">
                <img src="img\team\team-3.jpg" alt="team-3">
                <h3>Trainer 3</h3>
                <p class="price">Rating: 4.8/5.0</p>
                <p>Role: Fitness Trainer</p>
                <button type="submit" class="delete">Delete</button>
            </div>
            <!-- Add more trainers as needed -->
        </div>
    </div>
</body>
</html>