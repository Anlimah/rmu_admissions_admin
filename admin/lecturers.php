<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}

require("../bootstrap.php");

use Core\Base;
use Controller\Lecturers;

$config = require Base::build_path("config/database.php");

$pageTitle = "Lecturers";
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewLecturer">
               ADD
              </button>
             <!-- End Vertically centered Modal-->
             <div class="modal fade" id="addNewLecturer" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add new lecturer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form id="addNewLecForm" class="row g-3" method="POST">
                                <div class="col-md-4">
                                    <label for="add-lec-prefix" class="form-label">Prefix</label>
                                    <select name="add-lec-prefix" id="add-lec-prefix" class="form-select">
                                        <option hidden>Choose...</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Miss">Miss</option>
                                    </select>
                                </div>
                               
                                <div class="col-md-4">
                                    <label for="add-lec-fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="add-lec-fname" name="add-lec-fname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="add-lec-mname" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="add-lec-mname" name="add-lec-mname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="add-lec-lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="add-lec-lname" name="add-lec-lname" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="add-lec-number" class="form-label">Staff Number</label>
                                    <input type="text" class="form-control" id="add-lec-number" name="add-lec-number" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-lec-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="add-lec-email" name="add-lec-email" value="">
                                </div>
                                <?php if (isset($_SESSION["user"]["role"]) && ($_SESSION["user"]["role"] === "lecturer" || $_SESSION["user"]["role"] === "hod")) { ?>
                <div class="col-md-4">
                    <label for="add-lec-role" class="form-label">Role</label>
                    <select id="add-lec-role" name="add-lec-role" class="form-select">
                        <option hidden>Choose...</option>
                        <option value="lecturer">LECTURER</option>
                        <?php if ($_SESSION["user"]["role"] === "hod") { ?>
                            <option value="hod">HOD</option>
                            <option value="secretary">SECRETARY</option>
                        <?php } ?>
                    </select>
                </div>
            <?php } elseif (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] === "secretary") { ?>
                <div class="col-md-4">
                    <label for="add-lec-role" class="form-label">Role</label>
                    <select id="add-lec-role" name="add-lec-role" class="form-select">
                        <option hidden>Choose...</option>
                        <option value="lecturer">LECTURER</option>
                    </select>
                </div>
            <?php } ?>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label">Gender</label>
                                    <div class="form-check">
                                        <input type="radio" name="add-lec-gender" id="maleGender" class="form-check-input" value="M">
                                        <label for="maleGender" class="form-check-label">M</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="add-lec-gender" id="femaleGender" class="form-check-input" value="F">
                                        <label for="femaleGender" class="form-check-label">F</label>
                                    </div>
                                </div>
                                <input type="hidden" name="add-lec-depart" value="<?= $_SESSION["user"]["fk_department"] ?>">
                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="add-lecturer" id="addNewLecBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div>
            </div>
          </div>

      </div>
    </div> 
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                  

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Lecturers</h5>
                                    <!-- Bordered Table -->
                                    <table class="table table-borderless datatable">                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Role</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $lecturers = new Lecturers($config["database"]["mysql"]);
                                            $all_lecturers = $lecturers->fetchByDepartment($_SESSION["user"]["fk_department"]);
                                            $counter = 1;
                                            foreach ($all_lecturers as $lecturer) :
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $counter ?></th>
                                                    <td><?= trim($lecturer["prefix"] . " " . $lecturer["first_name"] . " " . $lecturer["middle_name"] . " " . $lecturer["last_name"]) ?></td>
                                                    <td><?= $lecturer["role"] ?></td>
                                                    <td><?= $lecturer["gender"] ?></td>
                                                    <td style="display: flex;">
                                                        <button type="button" class="btn btn-primary btn-sm me-2 editLecturerData" data-lecturer="<?= $lecturer["number"] ?>" data-bs-toggle="modal" data-bs-target="#editLecturer">Edit</button>
                                                        <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $lecturer["number"] ?>">Archive</button>
                                                    </td>
                                                </tr>
                                            <?php
                                                $counter++;
                                            endforeach
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

        <!-- Add new lecturer modal -->
   <!-- End Full Screen Modal-->

        <!-- Edit Lectuer modal -->
        <div class="modal fade" id="editLecturer" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Lecturer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form class="row g-3" id="editLecForm" method="POST">
                                <div class="col-md-4">
                                    <label for="edit-lec-prefix" class="form-label">Prefix</label>
                                    <select id="edit-lec-prefix" name="edit-lec-prefix" class="form-select" value="">
                                        <option hidden>Choose...</option>
                                        <option value="Mr">MR</option>
                                        <option value="Mrs">MRS</option>
                                        <option value="Miss">MISS</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="edit-lec-fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="edit-lec-fname" name="edit-lec-fname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit-lec-mname" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit-lec-mname" name="edit-lec-mname" value="">
                                </div>
                                <div class="col-md-6">
                                    <label for="edit-lec-lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="edit-lec-lname" name="edit-lec-lname" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="edit-lec-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit-lec-email" name="edit-lec-email" value="">
                                </div>
                                <?php if (isset($_SESSION["user"]["role"])) { ?>
                                    <div class="col-md-4">
                                        <label for="edit-lec-role" class="form-label">Role</label>
                                        <select id="edit-lec-role" name="edit-lec-role" class="form-select" value="">
                                            <option hidden>Choose...</option>
                                            <?php if ($_SESSION["user"]["role"] === "hod") { ?>
                                                <option value="lecturer"> LECTURER </option>
                                                <option value="hod"> HOD </option>
                                                <option value="secretary"> SECRETARY </option>
                                            <?php } elseif ($_SESSION["user"]["role"] === "secretary") { ?>
                                                <option value="lecturer"> LECTURER </option>
                                            <?php } else { ?>
                                                <option value="lecturer"> LECTURER </option>
                                                <option value="hod"> HOD </option>
                                                <option value="secretary"> SECRETARY </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                
                                <input type="hidden" name="edit-lec-number" id="edit-lec-number" value="">
                            </form>
                            <!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="edit-lecturer" id="editLecBtn">Save changes</label>
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

            $(".editLecturerData").on("click", function() {
                lecID = this.dataset.lecturer;

                $.ajax({
                    type: "GET",
                    url: "../api/lecturer/fetch?staff=" + lecID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        $("#edit-lec-fname").val(data.message["first_name"]);
                        $("#edit-lec-mname").val(data.message["middle_name"]);
                        $("#edit-lec-lname").val(data.message["last_name"]);
                        $("#edit-lec-email").val(data.message["email"]);
                        $("#edit-lec-number").val(data.message["number"]);
                        $("#edit-lec-role").val(data.message["role"]);
                        $("#edit-lec-prefix").val(data.message["prefix"]); 
                    } else {
                        alert(data.message)
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#addNewLecBtn").on("click", function() {
                $("#addNewLecForm").submit();
            });

            $("#addNewLecForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/lecturer/add",
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

            $("#editLecBtn").on("click", function() {
                $("#editLecForm").submit();
            });

            $("#editLecForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/lecturer/edit",
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
                        "archive-lec-number": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/lecturer/archive",
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