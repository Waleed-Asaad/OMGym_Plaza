<?php
include 'connection.php';
session_start();

if(!isset($_SESSION['adminName'])){
   header('location:admin-login.php');
}

if(isset($_POST['add_image'])){
    $image = $_FILES['image']['name'];
    $target = "img/gallery/".basename($image);

    $sql = "INSERT INTO galary (image) VALUES ('$image')";
    if(mysqli_query($conn, $sql)){
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
            $message = "Image uploaded successfully";
        } else {
            $message = "Failed to upload image";
        }
    } else {
        $message = "Error adding image: " . mysqli_error($conn);
    }
}

if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $sql = "SELECT image FROM galary WHERE galaryID = '$delete_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $image = $row['image'];

    $sql = "DELETE FROM galary WHERE galaryID = '$delete_id'";
    if(mysqli_query($conn, $sql)){
        if(file_exists("img/gallery/".$image)){
            unlink("img/gallery/".$image);
        }
        $message = "Image deleted successfully";
    } else {
        $message = "Error deleting image: " . mysqli_error($conn);
    }
}

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
        .card{
            height: auto;
        }
    </style>
</head>
<body>
    <?php include 'admin-menu.php'; ?>
    <div class="add-image">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="image" required>
                <input type="submit" name="add_image" value="Add Image" class="add-image-btn">
            </form>
        </div>
    <div class="container">
        <?php
        if(isset($message)){
            echo "<div class='message'>$message</div>";
        }
        ?>
        
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
                        <img src='img/gallery/$image' alt='Gallery Image'>
                        <a href='admin-gallery.php?delete_id=$galaryID' class='delete' onclick='return confirm(\"Are you sure you want to delete this image?\")'>Delete</a>
                    </div>
                    ";
                }
            } else {
                echo "<p>No images found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
