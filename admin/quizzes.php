<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Core\Base;
use Controller\Quizzes;
use Controller\Sections;

$config = require Base::build_path("config/database.php");

$pageTitle = "Quizzes";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require Base::build_path("partials/head.php") ?>
</head>

<body>

    <?php require Base::build_path("partials/header.php") ?>

    <?php require Base::build_path("partials/aside.php") ?>

    <main id="main" class="main">

        <?php require Base::build_path("partials/page-title.php") ?>

        <section class="section dashboard">
        <div class="row">
        <div class="col-lg-12">
              <div class="card">
            <div class="card-body">
                <p></p>
              <!-- Vertically centered Modal -->
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewQuiz">
               ADD
              </button> 

              <!-- Add new course modal -->
              <div class="modal fade" id="addNewQuiz" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                <div style="display: flex; width: 800px; align-items: center;">
                    <!-- Multi Columns Form -->
                    <form class="row g-3" id="addNewQuizForm" method="POST">
    <!-- Dropdown for fk_course -->
    <div class="col-md-4">
                                                        <label for="course" class="form-label">Course</label>
          <select id="course" class="form-select" name="course">
          <option value="" hidden>Choose...</option>
            <?php
            $config = require Base::build_path("config/database.php");
          $classes = new Sections($config["database"]["mysql"]);
               $all_classes = $classes->fetchByStaffID($_SESSION["user"]["number"]);

        $counter = 0;
           foreach ($all_classes as $classes) :                                                ?>
         <option value="<?= $classes["fk_course"] ?>"> <?= trim($classes["name"]) ?></option>
                    <?php
          $counter++;
           endforeach
                ?>
                  </select>
 </div>

    <!-- Other input fields -->
    <div class="col-md-8">
        <label for="addTitle" class="form-label">Title</label>
        <input type="text" class="form-control" id="addTitle" name="addTitle">
    </div>
    <div class="col-md-12">
        <label for="addInstructions" class="form-label">Instructions</label>
        <textarea class="form-control" id="addInstructions" name="addInstructions"></textarea>
    </div>
    <div class="col-md-4">
        <label for="addStartDate" class="form-label">Start Date</label>
        <input type="date" class="form-control" id="addStartDate" name="addStartDate">
    </div>
    <div class="col-md-4">
        <label for="addStartTime" class="form-label">Start Time</label>
        <input type="time" class="form-control" id="addStartTime" name="addStartTime">
    </div>
    <div class="col-md-4">
        <label for="addDuration" class="form-label">Duration</label>
        <input type="text" class="form-control" id="addDuration" name="addDuration">
    </div>

    <!-- Hidden inputs for id, fk_staff, and fk_semester -->
    <input type="hidden" id="addFkStaff" name="addFkStaff" value="<?= $_SESSION["user"]["number"] ?>">
    <input type="hidden" id="addFkSemester" name="addFkSemester" value="<?= $_SESSION["user"]["fk_semester"] ?>">
    <input type="hidden" id="department" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>">
</form>

                    <!-- End Multi Columns Form -->
                </div>
            </div>
            <div class="modal-footer">
                <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                <label class="btn btn-primary" for="add-course" id="addNewQuizBtn">Save changes</label>
            </div>
        </div>
    </div>
</div>
<!-- End Full Screen Modal-->
            
            </div>
          </div>

      </div>
    </div>
            <div class="row">
               <!-- Left side columns -->
                <div class="col-lg-12">
                     <div class="row">
                        <div class="col-xxl-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Quizzes</h5>
                                    <!-- Borderless Table -->
                                    <table class="table table-borderless datatable">                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <!-- <th scope="col">Quiz Code</th> -->
                                                <th scope="col">Course Name</th>
                                                <th scope="col">Quiz Title</th>
                                                <th scope="col">Total Marks</th>             
                                                <th scope="col">Start Date</th>
                                                <th scope="col">Start Time</th>
                                                <th scope="col">Duration</th>
                                                <th scope="col">Status</th>


                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $quizzes = new Quizzes($config["database"]["mysql"]);
                                            $all_quizzes = $quizzes->fetchByStaffID($_SESSION["user"]["number"]);
                                            $rowCounter = 1;
                                            foreach ($all_quizzes as $quiz) {
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $rowCounter++ ?></th>
                                                    <!-- <td><?= $quiz["id"] ?></td> -->
                                                    <td><?= $quiz["name"] ?></td>
                                                    <td><?= $quiz["title"] ?></td>
                                                    <td><?= $quiz["total_mark"] ?></td>
                                                    <td><?= $quiz["start_date"] ?></td>                                                  
                                                    <td><?= $quiz["start_time"] ?></td>
                                                    <td><?= $quiz["duration"] ?></td>  
                                                    <td><?= $quiz["status"] ?></td>
                                            
                                                                                                        
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <!-- End Bordered Table -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Left side columns -->
            </div>
            

            </div>
        </section>
          <!-- Edit Lectuer modal -->
         <script>
                // editclassescript.js

                $(document).ready(function() {
           ("#addNewQuizBtn").on("click", function() {
                $("#addNewQuizForm").submit();
            });

            $("#addNewQuizForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/quiz/add",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    if (data.success) window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            });

                });
            </script>
        <!-- End Full Screen Modal-->
    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    

</body>

</html>