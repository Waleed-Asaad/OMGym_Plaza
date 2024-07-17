<?php 
include "connection.php";
?>

<div class="gallery-section gallery-page">
        <div class="gallery">
            <div class="grid-sizer"></div>
            <?php
            $sql = "SELECT * FROM galary";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="gs-item set-bg" data-setbg="img/gallery/' . $row['image'] . '">
                            <a href="img/gallery/' . $row['image'] . '" class="thumb-icon image-popup"><i class="fa fa-picture-o"></i></a>
                          </div>';
                }
            } else {
                echo '<p>No images found in the gallery.</p>';
            }
            ?>
        </div>
    </div>