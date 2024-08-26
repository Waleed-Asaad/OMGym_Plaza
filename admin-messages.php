<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['adminUsername'])) {
    header('location:admin-login.php');
    exit();
}

$adminUsername = $_SESSION['adminUsername'];

// Fetch adminId from the admin table
$adminQuery = "SELECT adminId FROM admin WHERE adminUsername = '$adminUsername'";
$adminResult = mysqli_query($conn, $adminQuery);
$adminRow = mysqli_fetch_assoc($adminResult);
$adminId = $adminRow['adminId'];

// Handle message status update
if (isset($_POST['messageId'])) {
    $messageId = $_POST['messageId'];
    $updateMessageQuery = "UPDATE admin_messages SET readed = 1 WHERE messageId = '$messageId' AND adminId = '$adminId'";
    mysqli_query($conn, $updateMessageQuery);
}

// Handle deletion of read messages
if (isset($_POST['deleteReadMessages'])) {
    $deleteQuery = "DELETE FROM admin_messages WHERE adminId = '$adminId' AND readed = 1";
    mysqli_query($conn, $deleteQuery);
    header('location:admin-messages.php'); // Redirect to avoid resubmission
}

// Fetch admin messages
$messagesQuery = "SELECT * FROM admin_messages WHERE adminId = '$adminId' ORDER BY messageId DESC";
$messagesResult = mysqli_query($conn, $messagesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messages</title>
    <style>
        <?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?>
        table {
            width: 60%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th.content-column, td.content-column {
            width: 80%;
        }

        th.status-column, td.status-column {
            width: 20%;
        }

        tr.unread {
            background-color: #ffefc1;
        }

        tr.read {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .message-content {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>

    <div class="messages-section">
        <h1 style="text-align:center; margin:20px 10px;">Admin Messages</h1>

        <form method="post" action="" style="text-align: left; margin-left: 20%;">
            <button type="submit" name="deleteReadMessages" style="background-color: red; color: white; padding: 10px; margin-bottom: 20px;">Delete Messages</button>
        </form>

        <?php if (mysqli_num_rows($messagesResult) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th class="content-column">Message</th>
                        <th class="status-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($messageRow = mysqli_fetch_assoc($messagesResult)) { ?>
                        <tr class="<?php echo $messageRow['readed'] == 0 ? 'unread' : 'read'; ?>">
                            <td class="content-column message-content" onclick="document.getElementById('messageForm<?php echo $messageRow['messageId']; ?>').submit();">
                                <?php echo $messageRow['content']; ?>
                            </td>
                            <td class="status-column">
                                <?php echo $messageRow['readed'] == 0 ? 'Unread' : 'Read'; ?>
                            </td>
                            <form id="messageForm<?php echo $messageRow['messageId']; ?>" method="post" action="">
                                <input type="hidden" name="messageId" value="<?php echo $messageRow['messageId']; ?>">
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p style="text-align: left; margin-left: 45%;">No messages found.</p>
        <?php } ?>
    </div>
</body>
</html>
