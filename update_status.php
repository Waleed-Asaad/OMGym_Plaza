<?php
session_start();
include 'connection.php';

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['userId'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['userId'];

// עדכון הסטטוס של המשתמש ל-trainee
$sql = "UPDATE user SET status = 'trainee' WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // הוספת פרטי המשתמש לטבלת trainee
    $sql = "INSERT INTO trainee (userId) VALUES (?)";
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
