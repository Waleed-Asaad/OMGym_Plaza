<?php
include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];

    // Step 1: Fetch all trainees associated with the trainer
    $select_trainees_sql = "SELECT traineeId, userId FROM trainee WHERE trainerId = '$delete_id'";
    $result_trainees = mysqli_query($conn, $select_trainees_sql);

    // Step 2: Update each trainee's trainerId to NULL
    if($result_trainees && mysqli_num_rows($result_trainees) > 0) {
        while($row_trainee = mysqli_fetch_assoc($result_trainees)) {
            $traineeId = $row_trainee['traineeId'];
            $userId = $row_trainee['userId'];
            
            // Set trainerId to NULL
            $update_trainee_sql = "UPDATE trainee SET trainerId = NULL WHERE traineeId = '$traineeId'";
            mysqli_query($conn, $update_trainee_sql);
            
            // Step 3: Send a message to each trainee
            $message_content = "Your trainer has been fired. Please choose a new trainer.";
            $insert_message_sql = "INSERT INTO messages (content, userId, trainerId, traineeId) VALUES ('$message_content', '$userId', '$delete_id', '$traineeId')";
            mysqli_query($conn, $insert_message_sql);
        }
    }

    // Step 4: Delete the trainer from the trainer and user tables
    $select_user_id_sql = "SELECT userId FROM trainer WHERE trainerId = '$delete_id'";
    $result = mysqli_query($conn, $select_user_id_sql);
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['userId'];
    
    $delete_trainer_sql = "DELETE FROM trainer WHERE trainerId = '$delete_id'";
    if(mysqli_query($conn, $delete_trainer_sql)){
        $delete_user_sql = "DELETE FROM user WHERE userId = '$user_id'";
        mysqli_query($conn, $delete_user_sql);
        
        header('location:trainers-edit.php');
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
        .trainers {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            width: 250px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .card img {
            width: 100%;
            height: auto;
        }
        .cancel-warning-orange {
            background-color: orange;
            color: white;
            padding: 5px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .cancel-warning-red {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
            margin-top: 10px;
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
                        $trainerId = $row['trainerId'];
                        $trainerName = $row['trainerName'];
                        $trainerImg = $row['trainerImg'];
                        $rating = $row['rating'];
                        $muscle_building = $row['muscle_building'];
                        $weight_loss = $row['weight_loss'];
                        $strength = $row['strength'];
                        $endurance = $row['endurance'];
                        $body_building = $row['body_building'];
                        $flexibility = $row['flexibility'];
                        $cancel_count = $row['cancel'];
                        
                        echo "
                        <div class='card'>
                            <img src='img/team/$trainerImg' alt='$trainerName'>
                            <h3>$trainerName</h3>
                            <p class='price'>Rating: $rating/5</p>";

                        // הצגת מספר הביטולים עם רקע בהתאם
                        if ($cancel_count >= 5 && $cancel_count < 10) {
                            echo "<p class='cancel-warning-orange'>Cancellations: $cancel_count</p>";
                        } elseif ($cancel_count >= 10) {
                            echo "<p class='cancel-warning-red'>Cancellations: $cancel_count</p>";
                        } else {
                            echo "<p>Cancellations: $cancel_count</p>";
                        }

                        echo "<p>Roles:</p>";
                        
                        if ($muscle_building) {
                            echo "<p>Muscle Building</p>";
                        }
                        if ($weight_loss) {
                            echo "<p>Weight Loss</p>";
                        }
                        if ($strength) {
                            echo "<p>Strength</p>";
                        }
                        if ($endurance) {
                            echo "<p>Endurance</p>";
                        }
                        if ($body_building) {
                            echo "<p>Bodybuilding</p>";
                        }
                        if ($flexibility) {
                            echo "<p>Flexibility</p>";
                        }
                        
                        echo "
                            <a href='trainers-edit.php?delete_id=$trainerId' class='delete' onclick='return confirm(\"Are you sure you want to delete this trainer?\")'>Delete</a>
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
