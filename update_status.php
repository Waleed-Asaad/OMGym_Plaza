<?php
session_start();
include 'connection.php';

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['userEmail'])) {
    die("User not logged in.");
}

$user_email = $_SESSION['userEmail'];
$membership_id = isset($_POST['period']) ? $_POST['period'] : 0;

$select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
$result = mysqli_query($conn, $select); 
$row = mysqli_fetch_array($result);
$trainee_name = $row['userName'];
$user_id = $row['userId'];

// עדכון הסטטוס של המשתמש ל-trainee
$sql = "UPDATE user SET status = 'trainee' WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // הוספת פרטי המשתמש לטבלת trainee
    $sql = "INSERT INTO trainee (userId, traineeName, membershipId) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $user_id, $trainee_name, $membership_id);

    if ($stmt->execute()) {
        echo "User status updated to trainee.";
    } else {
        echo "Error inserting into trainee table: " . $stmt->error;
    }
} else {
    echo "Error updating user status: " . $stmt->error;
}

$stmt->close();

$select1 = " SELECT * FROM trainee WHERE userId = '$user_id'  ";
$result1 = mysqli_query($conn, $select1); 
$row1 = mysqli_fetch_array($result1);
$trainee_id = $row1['traineeId'];

$days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

foreach ($days as $day) {
    $sql = "INSERT INTO traineeDay (days, traineeId) VALUES ('$day', $trainee_id)";
    if (mysqli_query($conn, $sql)){
        $select1 = " SELECT * FROM traineeDay WHERE days = '$day' AND traineeId = '$trainee_id'  ";
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
           $sql = "INSERT INTO traineeHours (hours, dayId) VALUES ('$hour', $day_id)";
           if (!mysqli_query($conn, $sql)) {
              echo "Error: " . mysqli_error($conn);
          }
        }
    } 
}
?>
