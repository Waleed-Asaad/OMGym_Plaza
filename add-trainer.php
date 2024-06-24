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
    $image = $_FILES['image']['name'];
    
    $muscle_building = isset($_POST['muscle_building']) ? 1 : 0;
    $weight_loss = isset($_POST['weight_loss']) ? 1 : 0;
    $strength = isset($_POST['strength']) ? 1 : 0;
    
    $sql = "INSERT INTO trainer (trainerName, trainerImg, muscle_building, weight_loss, strength) VALUES ('$trainerName', '$image', '$muscle_building', '$weight_loss', '$strength')";
    
    if (mysqli_query($conn, $sql)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $msg = "Trainer added successfully";
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
        <div>
            <label>Specialty:</label><br>
            <input type="checkbox" id="muscle_building" name="muscle_building" value="muscle_building">
            <label for="muscle_building">Muscle Building</label><br>
            <input type="checkbox" id="weight_loss" name="weight_loss" value="weight_loss">
            <label for="weight_loss">Weight Loss</label><br>
            <input type="checkbox" id="strength" name="strength" value="strength">
            <label for="strength">Strength</label><br>
        </div>
        <input type="file" name="image" accept="image/png, image/jpg, image/jpeg" required>
        <input type="submit" name="submit" value="Add" class="form-btn" required>
    </form>
</div>
<p><?php echo $msg; ?></p>
</body>
</html>
