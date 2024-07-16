<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['adminName'])) {
    header('location:admin-login.php');
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_membership'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $update_sql = "UPDATE membership SET name = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $name, $price, $id);

    if ($stmt->execute()) {
        $message = "Membership updated successfully!";
    } else {
        $message = "Error updating membership: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM membership";
$result = mysqli_query($conn, $sql);

$memberships = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $memberships[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Memberships</title>
    <style>
        <?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?>
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <h2 class="h2">Manage Memberships</h2> 
    <div class="membership-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Period (Months)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memberships as $membership) { ?>
                        <tr>
                            <form method="post" action="">
                                <td><input type="text" name="name" value="<?php echo $membership['name']; ?>"></td>
                                <td><input type="number" name="price" value="<?php echo $membership['price']; ?>"></td>
                                <td><?php echo $membership['period']; ?></td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $membership['id']; ?>">
                                    <input type="submit" name="update_membership" value="Update" class="membership-updateBtn">
                                </td>
                            </form>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
    </div>
</body>
</html>
