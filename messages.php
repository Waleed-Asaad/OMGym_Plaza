<?php 
include "connection.php";
session_start();

// בדיקת אימייל המשתמש מ-SESSION
$user_email = $_SESSION['userEmail'];

// שליפת userId לפי המייל
$sql = "SELECT userId FROM user WHERE userEmail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$user_id = $row['userId'];

$select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
    $result1 = mysqli_query($conn, $select); 
    $row1 = mysqli_fetch_array($result1);
    if($row1['status']=="trainee"){

        $sql = "SELECT traineeId FROM trainee WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $traineeId = $row['traineeId'];

        $sql = "SELECT * FROM messages WHERE traineeId = ? ORDER BY messageId DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $traineeId);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];

        while($message = $result->fetch_assoc()) {
            $messages[] = $message; // Store each message in an array
        }
    }
    else if($row1['status']=="trainer"){
        // שליפת ההודעות של המשתמש מהמסד נתונים

        $sql = "SELECT trainerId FROM trainer WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $trainerId = $row['trainerId'];

        $sql = "SELECT * FROM messages WHERE trainerId = ? ORDER BY messageId DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $trainerId);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];

        while($message = $result->fetch_assoc()) {
            $messages[] = $message; // Store each message in an array
        }
    }
    else{
        $sql = "SELECT * FROM messages WHERE userId = ? ORDER BY messageId DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];

        while($message = $result->fetch_assoc()) {
            $messages[] = $message; // Store each message in an array
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
        /* עיצוב בסיסי לטבלה */
        .table thead th {
            color: white;
            background-color: #343a40;
        }

        .table tbody tr.unread {
            background-color: #f8d7da; /* רקע אדום בהיר להודעות שלא נקראו */
        }

        .table tbody tr.read {
            background-color: #fff; /* רקע לבן להודעות שנקראו */
        }

        .table tbody tr:hover {
            background-color: #e9ecef; /* רקע אפור בהיר כאשר מרחפים */
        }
    </style>
</head>

<body>
<?php
    $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
    $result1 = mysqli_query($conn, $select); 
    $row1 = mysqli_fetch_array($result1);
    if($row1['status']=="trainee"){
        include 'traineeMenu.php';
    }
    else if($row1['status']=="user"){
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
                            <td colspan="3" class="text-center">No messages found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php
    $select = " SELECT * FROM user WHERE userEmail = '$user_email'  ";
    $result1 = mysqli_query($conn, $select); 
    $row1 = mysqli_fetch_array($result1);
    if($row1['status']=="trainee"){
        
    }
    else if($row1['status']=="user"){
        include 'userMenu.php';
    }
    else{
        include 'trainer_menu.php';
    }
?>
    <!-- Messages Section End -->

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
        // פונקציה לסימון הודעה כנקראה
        function markAsRead(messageId) {
            $.ajax({
                url: 'markAsRead.php',
                type: 'POST',
                data: { messageId: messageId },
                success: function(response) {
                    location.reload(); // רענון הדף לאחר שינוי
                }
            });
        }
    </script>

</body>

</html>
