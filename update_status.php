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
$membership_id=$_SESSION['priod'];

$select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
$result = mysqli_query($conn, $select); 
$row = mysqli_fetch_array($result);
$user_id = $row['userId'];
// עדכון הסטטוס של המשתמש ל-trainee
$sql = "UPDATE user SET status = 'trainee' WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // הוספת פרטי המשתמש לטבלת trainee
    $sql = "INSERT INTO trainee (userId,membershipId) VALUES (?,'$membership_id')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "User status updated to trainee.";
    } else {
        echo "Error inserting into trainee table: " . $stmt->error;
    }
} else {
    echo "Error updating user status: " . $stmt->error;
}

$stmt->close();
?>
