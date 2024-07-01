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
</head>

<body>
<?php
    include 'menu.php';
?>

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb-bg.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb-text">
                        <h2>BMI calculator</h2>
                        <div class="bt-option">
                            <a href="./index.html">Home</a>
                            <a href="#">Pages</a>
                            <span>BMI calculator</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- BMI Calculator Section Begin -->
    <section class="bmi-calculator-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title chart-title">
                        <span>check your body</span>
                        <h2>BMI CALCULATOR CHART</h2>
                    </div>
                    <div class="chart-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bmi</th>
                                    <th>WEIGHT STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="point">Below 18.5</td>
                                    <td>Underweight</td>
                                </tr>
                                <tr>
                                    <td class="point">18.5 - 24.9</td>
                                    <td>Healthy</td>
                                </tr>
                                <tr>
                                    <td class="point">25.0 - 29.9</td>
                                    <td>Overweight</td>
                                </tr>
                                <tr>
                                    <td class="point">30.0 - and Above</td>
                                    <td>Obese</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-title chart-calculate-title">
                        <span>check your body</span>
                        <h2>CALCULATE YOUR BMI</h2>
                    </div>
                    <div class="chart-calculate-form">
                    <p>Maintaining a healthy BMI is crucial for overall well-being.
                         Use this calculator to find your BMI and assess your body weight status.</p>
                        <form id="bmiForm" onsubmit="calculateBMI(event)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" id="height" placeholder="Height / cm">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" id="weight" placeholder="Weight / kg">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" id="age" placeholder="Age">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" id="sex" placeholder="Sex">
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit">Calculate</button>
                                </div>
                                <div class="col-lg-12">
                                    <p id="bmiResult"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- BMI Calculator Section End -->

    <!-- Get In Touch Section Begin -->
    <?php 
        include 'getInTouch.php';
    ?>
    <!-- Get In Touch Section End -->

    <!-- Footer Section Begin -->
    <?php 
        include 'footer.php';
    ?>
    <!-- Footer Section End -->

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
        function calculateBMI(event) {
            event.preventDefault();
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);
            const age = parseInt(document.getElementById('age').value);
            const sex = document.getElementById('sex').value.toLowerCase();
            const bmi = (weight / ((height / 100) ** 2)).toFixed(1);

            let status;
            if (sex === 'male') {
                if (age < 18) {
                    if (bmi < 18.5) {
                        status = 'Underweight';
                    } else if (bmi >= 18.5 && bmi < 24.9) {
                        status = 'Healthy';
                    } else if (bmi >= 25 && bmi < 29.9) {
                        status = 'Overweight';
                    } else if(bmi >= 30){
                        status = 'Obese';
                    }
                } else {
                    if (bmi < 20) {
                        status = 'Underweight';
                    } else if (bmi >= 20 && bmi < 25) {
                        status = 'Healthy';
                    } else if (bmi >= 25 && bmi < 30) {
                        status = 'Overweight';
                    } else if(bmi >= 30){
                        status = 'Obese';
                    }
                }
            } else if (sex === 'female') {
                if (age < 18) {
                    if (bmi < 18.5) {
                        status = 'Underweight';
                    } else if (bmi >= 18.5 && bmi < 24.9) {
                        status = 'Healthy';
                    } else if (bmi >= 25 && bmi < 29.9) {
                        status = 'Overweight';
                    } else if(bmi >= 30){
                        status = 'Obese';
                    }
                } else {
                    if (bmi < 18.5) {
                        status = 'Underweight';
                    } else if (bmi >= 18.5 && bmi <= 24.9) {
                        status = 'Healthy';
                    } else if (bmi >= 25 && bmi < 29.9) {
                        status = 'Overweight';
                    } else if(bmi >= 30){
                        status = 'Obese';
                    }
                }
            } else {
                status = 'Invalid sex';
            }

            document.getElementById('bmiResult').innerText = `Your BMI is ${bmi}, which is considered ${status}.`;
        }
    </script>

</body>

</html>
