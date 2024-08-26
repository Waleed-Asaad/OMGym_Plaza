<?php 
include "connection.php";
session_start();

// Fetch user details based on session email
$user_email = $_SESSION['userEmail'];
$sql = "SELECT userId, status, userName FROM user WHERE userEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['userId'];
$user_status = $user['status'];
$user_name = $user['userName'];
$sql = "SELECT * FROM messages WHERE userId = $user_id ORDER BY messageId DESC";
$result2 = $conn->query($sql); 
$messages = $result2->fetch_all(MYSQLI_ASSOC);

// Fetch messages based on user status
if ($user_status == "trainee") {
    $sql = "SELECT traineeId, trainerId FROM trainee WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trainee = $result->fetch_assoc();
    $traineeId = $trainee['traineeId'];
    $trainerId = $trainee['trainerId'];
    $sql = "SELECT * FROM messages WHERE traineeId = $traineeId ORDER BY messageId DESC";
    $result2 = $conn->query($sql); 
    $messages = $result2->fetch_all(MYSQLI_ASSOC);
}
if ($user_status == "trainer") {
    $sql = "SELECT trainerId FROM trainer WHERE userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trainer = $result->fetch_assoc();
    $trainerId = $trainer['trainerId'];

    // Fetch the list of trainees associated with the trainer
    $sql = "SELECT traineeId, traineeName FROM trainee WHERE trainerId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $trainerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $trainees = $result->fetch_all(MYSQLI_ASSOC);
    $sql = "SELECT * FROM messages WHERE trainerId = $trainerId ORDER BY messageId DESC";
    $result2 = $conn->query($sql); 
    $messages = $result2->fetch_all(MYSQLI_ASSOC);

}



// Handle message deletion
if (isset($_POST['deleteReadMessages'])) {
    $sql = "DELETE FROM messages WHERE userId = ? AND readed = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page to reflect the changes
    exit();
}

// Handle message sending
if (isset($_POST['sendMessage'])) {
    $recipientType = $_POST['recipientType'];
    $messageContent = $_POST['messageContent'];
    $messageContent = "{$user_name}: $messageContent";

    if ($recipientType == 'trainer' && $user_status == 'trainee') {
        // Send message from trainee to trainer
        // שליפת ה-userId של המאמן מטבלת trainer לפי trainerId
        $sql = "SELECT userId FROM trainer WHERE trainerId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $trainerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $trainerData = $result->fetch_assoc();
        $trainerUserId = $trainerData['userId']; // זהו ה-userId של המאמן
        $sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, ?, 0, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $messageContent, $trainerUserId, $trainerId);
        $stmt->execute();

    } elseif ($recipientType == 'trainee' && $user_status == 'trainer') {
        // Send message from trainer to a specific trainee
        $recipientId = $_POST['recipientId'];
        $sql = "SELECT userId FROM trainee WHERE traineeId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $recipientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $traineeData = $result->fetch_assoc();
        $traineeUserId = $traineeData['userId'];
        $sql = "INSERT INTO messages (content, readed, userId, traineeId, trainerId) VALUES (?, 0, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $messageContent, $traineeUserId, $recipientId);
        $stmt->execute();


    } elseif ($recipientType == 'admin') {
        // Send message from user/trainee/trainer to admin
        $sql = "SELECT adminId FROM admin LIMIT 1";  // Assuming there's at least one admin
        $result = $conn->query($sql);
        $admin = $result->fetch_assoc();
        $adminId = $admin['adminId'];

        
        $sql = "INSERT INTO admin_messages (content, readed, adminId) VALUES (?, 0, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $messageContent, $adminId);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Gym Template">
    <meta name="keywords" content="Gym, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gym | Template</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <style>
        .table thead th {
            color: white;
            background-color: #343a40;
        }

        .table tbody tr.unread {
            background-color: #f8d7da;
        }

        .table tbody tr.read {
            background-color: #fff;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
<?php
    if($user_status == "trainee"){
        include 'traineeMenu.php';
    }
    elseif($user_status == "user"){
        include 'userMenu.php';
    }
    else{
        include 'trainer_menu.php';
    }
?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb-text">
                    <h2>Your Messages</h2>
                    <div class="bt-option">
                        <a href="./index.html">Home</a>
                        <span>Messages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Messages Section Begin -->
<section class="pricing-section spad">
    <div class="container">
        <!-- Delete Read Messages Button -->
        <form action="" method="post">
            <button type="submit" name="deleteReadMessages" class="form-btn" style="background-color: #f36105;">Delete Read Messages</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Content</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (count($messages) > 0) {
                    foreach ($messages as $message) { 
                        $rowClass = $message['readed'] == 0 ? 'unread' : 'read';
                        ?>
                        <tr class="<?php echo $rowClass; ?>" onclick="markAsRead(<?php echo $message['messageId']; ?>)" style="cursor:pointer;">
                            <td><?php echo htmlspecialchars($message['content']); ?></td>
                            <td><?php echo $message['readed'] == 0 ? 'Unread' : 'Read'; ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="2" class="text-center">No messages found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        
    </div>
</section>

<!-- Message Sending Section Begin -->
<section class="pricing-section spad">
    <div class="container">
        <form action="" method="post" style="width:800px;">
            <h1 style="font-size:35px;margin-bottom: 0; color: #f36105;">Send Message</h1>

            <?php if ($user_status == "trainer"): ?>
                <!-- Trainer can choose between sending to a trainee or admin -->
                <label for="recipientType" style="color:#f36105">Send to:</label>
                <select id="recipientType" name="recipientType" onchange="toggleTraineeDropdown(this.value)" required>
                    <option value="trainee">Trainee</option>
                    <option value="admin">Admin</option>
                </select>

                <div id="traineeDropdown" style="display:block;">
                    <label for="recipientId" style="color:#f36105">Select Trainee:</label>
                    <select id="recipientId" name="recipientId" required>
                        <?php foreach ($trainees as $trainee): ?>
                            <option value="<?php echo $trainee['traineeId']; ?>"><?php echo $trainee['traineeName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            <?php elseif ($user_status == "trainee"): ?>
                <!-- Trainee can choose between sending to their trainer or admin -->
                <label for="recipientType" style="color:#f36105">Send to:</label>
                <select id="recipientType" name="recipientType" required>
                    <option value="trainer">Trainer</option>
                    <option value="admin">Admin</option>
                </select>

            <?php else: ?>
                <!-- User can send to admin -->
                <label for="recipientTypeAdmin" style="color:#f36105">Send to Maneger:</label>
                <input type="hidden" id="recipientTypeAdmin" name="recipientType" value="admin">

            <?php endif; ?>

            <br>
            <label for="messageContent" style="color:#f36105">Message:</label>
            <textarea id="messageContent" name="messageContent" style="width:100%;" required></textarea>

            <input type="submit" name="sendMessage" value="Send" style="background-color: #f36105;" class="form-btn">
        </form>
    </div>
</section>
<!-- Message Sending Section End -->

<!-- Get In Touch Section Begin -->
<?php 
    include 'getInTouch.php';
?>
<!-- Get In Touch Section End -->

<!-- Js Plugins -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/masonry.pkgd.min.js"></script>
<script src="js/jquery.barfiller.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script>
    function markAsRead(messageId) {
        $.ajax({
            url: 'markAsRead.php',
            type: 'POST',
            data: { messageId: messageId },
            success: function(response) {
                location.reload();
            }
        });
    }

    function toggleTraineeDropdown(value) {
        if (value === 'trainee') {
            document.getElementById('traineeDropdown').style.display = 'block';
        } else {
            document.getElementById('traineeDropdown').style.display = 'none';
        }
    }
</script>

</body>

</html>
