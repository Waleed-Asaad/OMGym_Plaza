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

$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

foreach ($days as $day) {
    $sql = "INSERT INTO traineeDay (days, traineeId) VALUES ('$day', $trainee_id)";
    if (mysqli_query($conn, $sql)){
        $select1 = " SELECT * FROM traineeDay WHERE days = '$day' AND traineeId = '$trainee_id'  ";
        $result1 = mysqli_query($conn, $select1); 
        $row1 = mysqli_fetch_array($result1);
        $day_id = $row1['dayId'];

        $hours = [
                 '07',
                 '08',
                 '09',
                 '10',
                 '11',
                 '12',
                 '13',
                 '14',
                 '15',
                 '16',
                 '17',
                 '18'
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
