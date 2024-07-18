<?php
include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if(isset($_POST['add_image'])){
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $image = $_FILES['image']['name'];
        $target = "img/gallery/".basename($image);

        $sql = "INSERT INTO galary (image) VALUES ('$image')";
        mysqli_query($conn, $sql);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        // מחיקת הטוקן כדי למנוע שימוש חוזר
        unset($_SESSION['token']);
    }
}

if(isset($_POST['delete_selected'])){
    if(!empty($_POST['selected_images'])){
        $selected_images = $_POST['selected_images'];
        foreach($selected_images as $image_id){
            $sql = "DELETE FROM galary WHERE galaryID = '$image_id'";
            mysqli_query($conn, $sql);
        }
    }
}

$_SESSION['token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gallery</title>
    <style>
        <?php include 'C:\wamp64\www\omgym_plaza\css\admin-style.css'; ?>
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        .card img {
            width: 100%;
            height: auto;
        }
        .card {
            height: auto;
            position: relative;
        }
        .checkbox-container {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <div class="add-image">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="add image">Add Photo : </label>
            <input type="file" name="image" required>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
            <input type="submit" name="add_image" value="Add Image" class="add-image-btn">
        </form>
    </div>
    <div class="container">
        <form action="" method="post">
            <input type="submit" name="delete_selected" value="Delete Selected" class="delete">
            <div class="gallery-container">
                <?php
                $sql = "SELECT * FROM galary ORDER BY galaryID";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $galaryID = $row['galaryID'];
                        $image = $row['image'];
                        
                        echo "
                        <div class='card'>
                            <div class='checkbox-container'>
                                <input type='checkbox' name='selected_images[]' value='$galaryID'>
                            </div>
                            <img src='img/gallery/$image' alt='Gallery Image'>
                        </div>
                        ";
                    }
                } else {
                    echo "<p>No images found.</p>";
                }
                ?>
            </div>
        </form>
    </div>
</body>
</html>
