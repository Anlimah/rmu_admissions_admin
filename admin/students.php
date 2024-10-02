<?php
session_start();
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Controller\Classes;
use Core\Base;
use Controller\Students;

$config = require Base::build_path("config/database.php");


$pageTitle = "Students";
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewStudent">
               ADD
              </button> 
              <div class="modal fade" id="addNewStudent" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add new student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form id="addNewStudForm" class="row g-3" method="POST">
                                <div class="col-md-4">
                                    <label for="add-stud-num" class="form-label">Index Number</label>
                                    <input type="text" class="form-control" id="add-stud-num" name="add-stud-num" value="">

                                </div>
                                <div class="col-md-8">
                                    <label for="add-stud-fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="add-stud-fname" name="add-stud-fname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="add-stud-mname" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="add-stud-mname" name="add-stud-mname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="add-stud-lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="add-stud-lname" name="add-stud-lname" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-stud-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="add-stud-email" name="add-stud-email" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="class-code" class="form-label">Class</label>
                                
                                <select id="class-code" class="form-select" name="class-code">
                                          <option value="" hidden>Choose...</option>
                                                <?php
                                                $config = require Base::build_path("config/database.php");
                                                $classes = new Classes($config["database"]["mysql"]);
                                                $all_classes = $classes->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                                $counter = 0;
                                                foreach ($all_classes as $classes) : ?>
                                                    <option value="<?= $classes["classCode"] ?>"> <?= trim($classes["classCode"]) ?></option>
                                                <?php
                                                    $counter++;
                                                endforeach
                                                ?>
                                            </select>
                                            <div class="col-md-4">
        <label class="form-label">Gender</label>
        <div class="form-check">
            <input type="radio" name="add-stud-gender" id="malegender" class="form-check-input" value="M">
            <label for="malegender" class="form-check-label">M</label>
        </div>
        <div class="form-check">
            <input type="radio" name="add-stud-gender" id="femalegender" class="form-check-input" value="F">
            <label for="femalegender" class="form-check-label">F</label>
        </div>
    </div>
                                <input type="hidden" name="add-stud-depart" value="<?= $_SESSION["user"]["fk_department"] ?>">
                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="add-student" id="addNewStudBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div>
             
             <!-- End Vertically centered Modal-->
            
            </div>
          </div>

      </div>
    </div> 
                    <div class="row">
                         <div class="col-lg-12">
                            <div class="card">
                            <div class="card-body">
                                    <h5 class="card-title">Students</h5>

                                    <table class="table table-borderless datatable">
                                        <thead>
                                        <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Name</th>
                                                                    <th scope="col">Index Number</th>
                                                                    <th scope="col">Class</th>
                                                                    <th scope="col"></th>
                                                                </tr>
                                        </thead>
                                    
                                        <tbody>
                                                                <?php
                                                                $students = new Students($config["database"]["mysql"]);
                                                                $all_students = $students->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                                                $counter = 1;
                                                                foreach ($all_students as $student) :
                                                                ?>
                                                                    <tr>
                                                                        <th scope="row"><?= $counter ?></th>
                                                                        <td><?= trim($student["firstName"] . " " . $student["middleName"] . " " . $student["lastName"]) ?></td>
                                                                        <td><?= $student["indexNumber"] ?></td>
                                                                        <td><?= $student["studClass"] ?></td>

                                                                        <td style="display: flex;">
                                                                            <button type="button" class="btn btn-primary btn-sm me-2 editStudentData" data-student="<?= $student["indexNumber"] ?>" data-bs-toggle="modal" data-bs-target="#editStudent">Edit</button>
                                                                            <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $student["indexNumber"] ?>">Archive</button>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                    $counter++;
                                                                endforeach
                                                                ?>
                                                            </tbody>
                                    </table>

                                    </div>
                            </div>      
                            </div>
                        </div>
                     </div><!-- End Left side columns -->
            </div><!-- End Left side columns -->
           </div>
        </section>
        <!-- Edit Lectuer modal -->
        <div class="modal fade" id="editStudent" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                        <div style="display: flex; width: 600px; align-items: center;">
                            <!-- Multi Columns Form -->
                            <form class="row g-3" id="editStudForm" method="POST">
                              <div class="col-md-8">
                                    <label for="edit-stud-fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="edit-stud-fname" name="edit-stud-fname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit-stud-mname" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit-stud-mname" name="edit-stud-mname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit-stud-lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="edit-stud-lname" name="edit-stud-lname" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="edit-stud-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit-stud-email" name="edit-stud-email" value="">
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <label for="gender" class="form-label">Gender</label>
                                        <div class="form-check">
                                            <input type="radio" name="edit-stud-gender" id="maleGender" class="form-check-input" value="M">
                                            <label for="maleGender" class="form-check-label">M</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="edit-stud-gender" id="femaleGender" class="form-check-input" value="F">
                                            <label for="femaleGender" class="form-check-label">F</label>
                                        </div>
                                    </div>
                                </div>                      
             <input type="hidden" name="edit-stud-index" id="edit-stud-index" value="">

                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="edit-student" id="editStudBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div><!-- End Full Screen Modal-->
        <?php
        // endforeach;
        // endif;
        ?>

    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    <script>
        $(document).ready(function() {

            $(".editStudentData").on("click", function() {
                StudID = this.dataset.student;
                
                $.ajax({
                    type: "GET",
                    url: "../api/student/fetch?student=" + StudID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        // alert(data.message["firstName"])
                        $("#edit-stud-fname").val(data.message["firstName"]);
                        $("#edit-stud-mname").val(data.message["middleName"]);
                        $("#edit-stud-lname").val(data.message["lastName"]);
                        $("#edit-stud-email").val(data.message["email"]);
                        $("#edit-stud-index").val(data.message["indexNumber"]);  
                        $("#edit-stud-gender").val(data.message["gender"]);

                    } else {
                        alert(data.message)
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#addNewStudBtn").on("click", function() {
                $("#addNewStudForm").submit();
            });

            $("#addNewStudForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/student/add",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    if (data.success)
                    window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            })

            $("#editStudBtn").on("click", function() {
                $("#editStudForm").submit();
            });

            $("#editStudForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/student/edit",
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
                if (confirm("Are you sure you want to archive this student's information?")) {
                    formData = {
                        "archive-stud-number": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/student/archive",
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

</body>

</html>