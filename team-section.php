<section class="team-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="team-title">
                        <div class="section-title">
                            <span>Our Team</span>
                            <h2>TRAIN WITH EXPERTS</h2>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="ts-slider owl-carousel">
                <?php
                $sql = "SELECT * FROM trainer";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $trainerName = $row['trainerName'];
                        $trainerImg = $row['trainerImg'];
                        $rating = $row['rating'];
                        $muscle_building = $row['muscle_building'];
                        $weight_loss = $row['weight_loss'];
                        $strength = $row['strength'];
                        $endurance = $row['endurance'];
                        $body_building = $row['body_building'];
                        $flexibility = $row['flexibility'];
                        echo "
                <div class='col-lg-4 col-sm-6'>
                    <div class='ts-item set-bg' data-setbg='img/team/$trainerImg'>
                        <div class='ts_text'>
                            <h4>$trainerName</h4>";
                                if ($muscle_building) {
                                    echo "<span>Muscle Building trainer</span>";
                                }
                                if ($weight_loss) {
                                    echo "<span>Weight Loss trainer</span>";
                                }
                                if ($strength) {
                                    echo "<span>Strength trainer</span>";
                                }
                                if ($endurance) {
                                    echo "<span>Endurance trainer</span>";
                                }
                                if ($body_building) {
                                    echo "<span>Bodybuilding trainer</span>";
                                }
                                if ($flexibility) {
                                    echo "<span>Flexibility trainer</span>";
                                }
                            
                            echo "<span class='price'>Rating: $rating/5.0</span>";
                            echo "
                        </div>
                    </div>
                </div>";
            }
                } else {
                    echo '<p>No trainers found.</p>';
                }
            ?>
                </div>
            </div>
        </div>
    </section>