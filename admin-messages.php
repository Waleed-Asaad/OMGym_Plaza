<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['adminUsername'])) {
    header('location:admin-login.php');
    exit();
}

$adminUsername = $_SESSION['adminUsername'];

// Fetch adminId from the admin table
$adminQuery = "SELECT adminId FROM admin WHERE adminUsername = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("s", $adminUsername);
$stmt->execute();
$adminResult = $stmt->get_result();
$adminRow = $adminResult->fetch_assoc();
$adminId = $adminRow['adminId'];

// Handle message status update
if (isset($_POST['messageId'])) {
    $messageId = $_POST['messageId'];
    $updateMessageQuery = "UPDATE admin_messages SET readed = 1 WHERE messageId = ? AND adminId = ?";
    $stmt = $conn->prepare($updateMessageQuery);
    $stmt->bind_param("ii", $messageId, $adminId);
    $stmt->execute();
}

// Handle deletion of read messages
if (isset($_POST['deleteReadMessages'])) {
    $deleteQuery = "DELETE FROM admin_messages WHERE adminId = ? AND readed = 1";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    header('location:admin-messages.php'); // Redirect to avoid resubmission
}

// Pagination
$messagesPerPage = 15;  // Number of messages per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Current page number
$offset = ($page - 1) * $messagesPerPage;  // Offset for SQL query

// Fetch total number of messages
$totalMessagesQuery = "SELECT COUNT(*) as total FROM admin_messages WHERE adminId = ?";
$stmt = $conn->prepare($totalMessagesQuery);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$totalMessagesResult = $stmt->get_result();
$totalMessagesRow = $totalMessagesResult->fetch_assoc();
$totalMessages = $totalMessagesRow['total'];

// Calculate total pages
$totalPages = ceil($totalMessages / $messagesPerPage);

// Fetch admin messages with limit and offset
$messagesQuery = "SELECT * FROM admin_messages WHERE adminId = ? ORDER BY messageId DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($messagesQuery);
$stmt->bind_param("iii", $adminId, $messagesPerPage, $offset);
$stmt->execute();
$messagesResult = $stmt->get_result();
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

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: black;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
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

        <?php if ($messagesResult->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th class="content-column">Message</th>
                        <th class="status-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($messageRow = $messagesResult->fetch_assoc()) { ?>
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

            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php } ?>
            </div>

        <?php } else { ?>
            <p style="text-align: left; margin-left: 45%;">No messages found.</p>
        <?php } ?>
    </div>
</body>
</html>
