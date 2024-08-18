<?php
include "connection.php";
session_start();

if (isset($_POST['messageId'])) {
    $messageId = intval($_POST['messageId']);

    // עדכון ההודעה כנקראה
    $sql = "UPDATE messages SET readed = 1 WHERE messageId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $messageId);
    if ($stmt->execute()) {
        echo "Message marked as read.";
    } else {
        echo "Error updating message: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
