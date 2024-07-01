<?php

include 'connection.php';

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
<script>
        function enrollNow() {
            if (confirm("Are you sure you want to enroll?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        alert(xhr.responseText);
                        if (xhr.status === 200) {
                            window.location.href = "traineeHome.php";
                        }
                    }
                };
                xhr.send();
                return false;
            } else {
                return false;
            }
        }
    </script>
<section class="pricing-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Our Plans</span>
                        <h2>Choose your pricing plan</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-8">
                    <div class="ps-item">
                        <h3>Monthly</h3>
                        <div class="pi-price">
                            <h2>$ 49.0</h2>
                            <span>One Month Subscription</span>
                        </div>
                        <ul>
                            <li>Free riding</li>
                            <li>Unlimited equipments</li>
                            <li>Personal trainer</li>
                            <li>Weight losing classes</li>
                            <li>No time restriction</li>
                        </ul>
                        <a href="#" class="primary-btn pricing-btn" onclick="return enrollNow()" <?php $_SESSION['priod']=1 ?>>Enroll now</a>
                        
                    </div>
                </div>
                <div class="col-lg-4 col-md-8">
                    <div class="ps-item">
                        <h3>12 Month unlimited</h3>
                        <div class="pi-price">
                            <h2>$ 579.0</h2>
                            <span>One Year Subscription</span>
                        </div>
                        <ul>
                            <li>Free riding</li>
                            <li>Unlimited equipments</li>
                            <li>Personal trainer</li>
                            <li>Weight losing classes</li>
                            <li>Month to mouth</li>
                            <li>No time restriction</li>
                        </ul>
                        <a href="#" class="primary-btn pricing-btn" onclick="return enrollNow()" <?php $_SESSION['priod']=3 ?> >Enroll now</a>
                        
                    </div>
                </div>
                <div class="col-lg-4 col-md-8">
                    <div class="ps-item">
                        <h3>6 Months unlimited</h3>
                        <div class="pi-price">
                            <h2>$ 289.0</h2>
                            <span>6 Months Subscription</span>
                        </div>
                        <ul>
                            <li>Free riding</li>
                            <li>Unlimited equipments</li>
                            <li>Personal trainer</li>
                            <li>Weight losing classes</li>
                            <li>Month to mouth</li>
                            <li>No time restriction</li>
                        </ul>
                        <a href="#" class="primary-btn pricing-btn" onclick="return enrollNow()" <?php $_SESSION['priod']=2 ?> >Enroll now</a>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>