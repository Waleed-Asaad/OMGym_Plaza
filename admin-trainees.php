<?php

include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $select_user_id_sql = "SELECT userId FROM trainee WHERE traineeId = '$delete_id'";
    $result = mysqli_query($conn, $select_user_id_sql);
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['userId'];

    $delete_sql = "DELETE FROM trainee WHERE traineeId = '$delete_id'";
    if(mysqli_query($conn, $delete_sql)){
        $update_status_sql = "UPDATE user SET status = 'user' WHERE userId = '$user_id'";
        mysqli_query($conn, $update_status_sql);
        
        header('location:admin-trainees.php');
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
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
                $sql = "SELECT * FROM trainee ORDER BY traineeId";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $traineeId = $row['traineeId'];
                        $traineeName = $row['traineeName'];
                        $traineeImg = $row['traineeImg'];
                        $subscriptionDate = $row['startingMembership'];
                        
                        echo "
                        <div class='card'>
                            <img src='img/trainees/$traineeImg' alt='$traineeName'>
                            <h3>$traineeName</h3>
                            <h5>$subscriptionDate</h5>";
                        
                        echo "
                            <a href='admin-trainees.php?delete_id=$traineeId' class='delete' onclick='return confirm(\"Are you sure you want to delete this trainee?\")'>Delete</a>
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
