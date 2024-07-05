<?php

include 'connection.php';

session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-home.php');
}

$msg = "";
if(isset($_POST['submit'])){
    
    $target = "img/team/".basename($_FILES['image']['name']);
    $trainerName = $_POST['trainerName'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $image = $_FILES['image']['name'];
    
    $muscle_building = isset($_POST['muscle_building']) ? 1 : 0;
    $weight_loss = isset($_POST['weight_loss']) ? 1 : 0;
    $strength = isset($_POST['strength']) ? 1 : 0;
    $endurance = isset($_POST['endurance']) ? 1 : 0;
    $flexibility = isset($_POST['flexibility']) ? 1 : 0;
    $body_building = isset($_POST['body_building']) ? 1 : 0;
    
    $sql = "INSERT INTO user (userName, userEmail, userAddress, userPassword, status) VALUES ('$trainerName', '$email', '$address', '$password', 'trainer')";
    mysqli_query($conn, $sql);
    $select = " SELECT * FROM user WHERE userEmail = '$email'  ";
    $result = mysqli_query($conn, $select); 
    $row = mysqli_fetch_array($result);
    $user_id = $row['userId'];
    $sql = "INSERT INTO trainer (trainerName, trainerImg, muscle_building, weight_loss, strength,endurance, flexibility,body_building, userId) VALUES ('$trainerName', '$image', '$muscle_building', '$weight_loss', '$strength','$endurance','$flexibility','$body_building','$user_id')";
    if (mysqli_query($conn, $sql)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $msg = "Trainer added successfully";
            $select1 = " SELECT * FROM trainer WHERE userId = '$user_id'  ";
            $result1 = mysqli_query($conn, $select1); 
            $row1 = mysqli_fetch_array($result1);
            $trainer_id = $row1['trainerId'];

            $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

            foreach ($days as $day) {
                $sql = "INSERT INTO trainerDay (days, trainerId) VALUES ('$day', $trainer_id)";
                if (mysqli_query($conn, $sql)){
                    $select1 = " SELECT * FROM trainerDay WHERE days = '$day' &&trainerId = '$trainer_id'  ";
                    $result1 = mysqli_query($conn, $select1); 
                    $row1 = mysqli_fetch_array($result1);
                    $day_id = $row1['dayId'];

                    $hours = [
                             '7:00-8:00',
                             '8:00-9:00',
                             '9:00-10:00',
                             '10:00-11:00',
                             '11:00-12:00',
                             '12:00-13:00',
                             '13:00-14:00',
                             '14:00-15:00',
                             '15:00-16:00',
                             '16:00-17:00',
                             '17:00-18:00',
                             '18:00-19:00'
                             ];
        
                     foreach ($hours as $hour) {
                       $sql = "INSERT INTO trainerHours (hours, dayId) VALUES ('$hour', $day_id)";
                       if (!mysqli_query($conn, $sql)) {
                          echo "Error: " . mysqli_error($conn);
                      }
            }
         } 
                else{
                    echo "Error: " . mysqli_error($conn);
                }
            }

            
        } else {
            $msg = "Failed to upload image";
        }
    } else {
        $msg = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style><?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?></style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trainer</title>
</head>
<body>
<?php include 'admin-menu.php'; ?>
<div class="form-container">
    <form method="post" action="" enctype="multipart/form-data">
        <h3>Add a new trainer</h3>
        <input type="text" name="trainerName" placeholder="Enter trainer name" required>
        <input type="password" name="password" required placeholder="enter trainer password">
        <input type="text" name="address" required placeholder="enter trainer address">
        <input type="email" name="email" required placeholder="Enter trainer email">
        <div class="specialty">
            <label>Specialty:</label><br>
            <label for="muscle_building">Muscle Building</label><br>
            <input type="checkbox" id="muscle_building" name="muscle_building" value="muscle_building">
            <label for="weight_loss">Weight Loss</label><br>
            <input type="checkbox" id="weight_loss" name="weight_loss" value="weight_loss">
            <label for="strength">Strength</label><br>
            <input type="checkbox" id="strength" name="strength" value="strength">
            <label for="endurance">Endurance</label><br>
            <input type="checkbox" id="endurance" name="endurance" value="endurance">
            <label for="flexibility">Flexibility</label><br>
            <input type="checkbox" id="flexibility" name="flexibility" value="flexibility">
            <label for="body_building">Body Building</label><br>
            <input type="checkbox" id="body_building" name="body_building" value="body_building">
            
        </div>
        <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" required>
        <input type="submit" name="submit" value="Add" class="form-btn" required>
    </form>
</div>
<p><?php echo $msg; ?></p>
</body>
</html>
