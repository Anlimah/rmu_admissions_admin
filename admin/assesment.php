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

$config = require Base::build_path("config/database.php");

$pageTitle = "Assesment";
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
                                    <h5 class="card-title">Courses</h5>
                                    <!-- Borderless Table -->
                                    <table class="table table-borderless datatable">                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Course Code</th>
                                                <th scope="col">Course Name</th>
                                                <th scope="col">Credit Hours</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $courses = new Courses($config["database"]["mysql"]);
                                            $all_courses = $courses->fetchByDepartment($_SESSION["user"]["fk_department"]);
                                            $rowCounter = 1;
                                            foreach ($all_courses as $course) {
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $rowCounter++ ?></th>
                                                    <td><?= $course["courseCode"] ?></td>
                                                    <td><?= $course["courseName"] ?></td>
                                                    <td><?= $course["creditHours"] ?></td>
                                                    <td style="display: flex;">
                                                        <button type="button" class="btn btn-primary btn-sm me-2 editCourseData" data-course="<?= $course["courseCode"] ?>" data-bs-toggle="modal" data-bs-target="#editCourse">Edit</button>
                                                        
                                                            <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $course["courseCode"] ?>">Archive</button>
                                                         </td>
                                                                                                        
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
            </div><!-- End Left side columns -->

            </div>
        </section>
      

        <!-- Edit Lectuer modal -->
        <div class="modal fade" id="editCourse" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
               <?php

?>     <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                        <div style="display: flex; width: 600px; align-items: center;">


                            <!-- Multi Columns Form -->
                            <form class="row g-3" id="editCourseForm" method="POST">
    <div class="col-md-8">
        <label for="edit-course-code" class="form-label">Course Code</label>
        <input type="text" class="form-control" name="edit-course-code" id="edit-course-code" value="<?= $course["courseCode"] ?>">
    </div>
    <div class="col-md-8">
        <label for="edit-course-name" class="form-label">Course Name</label>
        <input type="text" class="form-control" name="edit-course-name" id="edit-course-name" value="<?= $course["courseName"] ?>">
    </div>
    <div class="col-md-8">
        <label for="edit-credit-hours" class="form-label">Credit Hours</label>
        <input type="text" class="form-control" name="edit-credit-hours" id="edit-credit-hours" value="<?= $course["creditHours"] ?>">
    </div>
    <input type="hidden" name="edit-course-code" id="edit-code" value="">
</form>
<!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" for="editButton" id="editCourseBtn">Save changes</button>
                    </div>
                </div>
            </div><!-- Include jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>



            <script>
                // editCourseScript.js

                $(document).ready(function() {
                    
            $(".editCourseData").on("click", function() {
                courseID = this.dataset.course;

                $.ajax({
                    type: "GET",
                    url: "../api/course/fetch?course=" + courseID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        $("#edit-course-code").val(data.message["courseCode"]);
                        $("#edit-course-name").val(data.message["courseName"]);
                        $("#edit-credit-hours").val(data.message["creditHours"]);
                        $("#edit-code").val(data.message["courseCode"]);

 // add staff number
                    } else {
                        alert(data.message)
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#editCourseBtn").on("click", function() {
                $("#editCourseForm").submit();
            });

            $("#editCourseForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/course/edit",
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


            $("#addNewCourseBtn").on("click", function() {
                $("#addNewCourseForm").submit();
            });

            $("#addNewCourseForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/course/add",
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



            $(".archiveBtn").on("click", function() {
                if (confirm("Are you sure you want to archive this staff information?")) {
                    formData = {
                        "archive-course-code": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/course/archive",
                        data: formData,
                    }).done(function(data) {
                        console.log(data);
                        alert(data.message);
                        if (data.success) window.location.reload();
                    }).fail(function(err) {
                        console.log(err);
                    });
                }
            });
                });
            </script>
        </div>
        <!-- End Full Screen Modal-->
    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    

</body>

</html>