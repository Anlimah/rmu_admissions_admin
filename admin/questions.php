<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Core\Base;
use Controller\Courses;
use Controller\Questions;

$config = require Base::build_path("config/database.php");

$pageTitle = "Questions";
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewCourse">
               ADD
              </button> 
              <!-- Add new course modal -->
        <div class="modal fade" id="addNewCourse" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                        <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                        <div style="display: flex; width: 800px; align-items: center;">
                            <!-- Multi Columns Form -->
                            <form class="row g-3" id="addNewCourseForm" method="POST">
                                <div class="col-md-8">
                                    <label for="addcourseCode" class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="addcourseCode" name="addcourseCode">
                                </div>
                                <div class="col-md-8">
                                    <label for="addcourseName" class="form-label">Course Name</label>
                                    <input type="text" class="form-control" id="addcourseName" name="addcourseName">
                                </div>
                                <div class="col-md-8">
                                    <label for="add`creditHours`" class="form-label">Credit Hours</label>
                                    <input type="text" class="form-control" id="addcreditHours" name="addcreditHours">
                                </div>

                                <input type="hidden" id="department" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>">

                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="add-course" id="addNewCourseBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div><!-- End Full Screen Modal-->
            
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
                                    <h5 class="card-title">Questions</h5>
                                    <!-- Borderless Table -->
                                    <?php
// Check if the fk_course parameter is set in the URL
if(isset($_GET["fk_course"])) {
    // Instantiate Questions class
    $questions = new Questions($config["database"]["mysql"]);

    // Fetch questions based on the course code obtained from the URL parameter
    $all_questions = $questions->fetchByCodeFromURL($_GET["fk_course"]);

    // Display the fetched questions in a table
    ?>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Type</th>
                <th scope="col">Question</th>
                <th scope="col">Marks</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Iterate over each question and display them in rows
            foreach ($all_questions as $rowCounter => $question): ?>
                <tr>
                    <th scope="row"><?= $rowCounter + 1 ?></th>
                    <td><?= $question["type"] ?></td>
                    <td><?= $question["question"] ?></td>
                    <td><?= $question["marks"] ?></td>
                </tr>
            <?php endforeach; ?> 
        </tbody>
    </table>
<?php
} else {
    // Display an error message if fk_course parameter is not set
    echo "Error: Course code is not provided.";
}
?>


<!-- End Bordered Table -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Left side columns -->
            </div><!-- End Left side columns -->

            </div>
        </section>
      

     
       
        <!-- End Full Screen Modal-->
    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    

</body>

</html>