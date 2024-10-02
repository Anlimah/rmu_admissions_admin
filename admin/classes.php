<?php
session_start();
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Controller\Programs;
use Controller\Classes;
use Core\Base;

$config = require Base::build_path("config/database.php");

$pageTitle = "Classes";
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
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewclass">
                                ADD
                            </button>
                            <!-- End Vertically centered Modal-->
                            <div class="modal fade" id="addNewclass" tabindex="-1">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add New Class</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                                            <div style="display: flex; width: 600px; align-items: center;">
                                                <!-- Multi Columns Form -->
                                                <form id="addNewClassForm" class="row g-3" method="POST">

                                                    <div class="col-md-8">
                                                        <label for="class_code" class="form-label">Class Code</label>
                                                        <input type="text" class="form-control" id="class_code" name="class_code">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="class_program" class="form-label">Program</label>
                                                        <select id="class_program" class="form-select" name="program">
                                                            <option value="" hidden>Choose...</option>
                                                            <?php
                                                            $config = require Base::build_path("config/database.php");
                                                            $programs = new Programs($config["database"]["mysql"]);
                                                            $all_programs = $programs->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                                            $counter = 0;
                                                            foreach ($all_programs as $programs) :                                                ?>
                                                                <option value="<?= $programs["programCode"] ?>"> <?= trim($programs["programName"]) ?></option>
                                                            <?php
                                                                $counter++;
                                                            endforeach
                                                            ?>
                                                        </select>

                                                    </div>

                                                </form><!-- End Multi Columns Form -->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                                            <label class="btn btn-primary" for="add-classes" id="addNewClassBtn">Save changes</label>
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
                    <!-- <div class="row mb-4"> -->
                    <!-- Add new lectuer modal -->
                    <!-- <div class="col-xxl-12 col-md-12"> -->
                    <!-- <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addNewclass">Add</button> -->
                    <!-- </div> -->
                    <!-- </div> -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Classes</h5>

                                    <!-- Borderless Table -->
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Class Code</th>
                                                <th scope="col">Program</th>
                                                <th scope="col">Program Name</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $classes = new Classes($config["database"]["mysql"]);
                                            $all_classes = $classes->fetchByDepartment($_SESSION["user"]["fk_department"]);
                                            $rowCounter = 1;
                                            foreach ($all_classes as $classes) {
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $rowCounter ?></th>
                                                    <td><?= $classes["classCode"] ?></td>
                                                    <td><?= $classes["programCode"] ?></td>
                                                    <td><?= $classes["programName"] ?></td>
                                                    <td style="display: flex;">
                                                        <button type="button" class="btn btn-primary btn-sm me-2 editClassData" data-class="<?= $classes["classCode"] ?>" data-bs-toggle="modal" data-bs-target="#editClass">Edit</button>
                                                        <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $classes["classCode"] ?>">Archive</button>
                                                    </td>
                                                </tr>
                                            <?php
                                                $rowCounter++;
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
        <!-- End Full Screen Modal-->

        <!-- Edit Lectuer modal -->
        <div class="modal fade" id="editClass" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                        <div style="display: flex; width: 600px; align-items: center;">
                            <!-- Multi Columns Form -->
                            <form class="row g-3" id="editClassForm" method="POST">


                                <div class="col-md-8">
                                    <label for="edit-class-code" class="form-label">Class Code</label>
                                    <input type="text" class="form-control" id="edit-class-code" name="edit-class-code" value="">
                                </div>
                               
                                <div class="col-md-4">
                                                        <label for="class_program" class="form-label">Program</label>
                                                        <select id="edit-class-program" class="form-select" name="edit-class-program">
                                                            <option value="" hidden>Choose...</option>
                                                            <?php
                                                            $config = require Base::build_path("config/database.php");
                                                            $programs = new Programs($config["database"]["mysql"]);
                                                            $all_programs = $programs->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                                            $counter = 0;
                                                            foreach ($all_programs as $programs) :                                                ?>
                                                                <option value="<?= $programs["programCode"] ?>"> <?= trim($programs["programName"]) ?></option>
                                                            <?php
                                                                $counter++;
                                                            endforeach
                                                            ?>
                                                        </select>

                                                    </div>
                                <input type="hidden" name="edit_class" id="edit_class" value="">

                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="edit-class" id="editClassBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div><!-- End Full Screen Modal-->

    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    <script>
        $(document).ready(function() {

            $(".editClassData").on("click", function() {
                classID = this.dataset.class;

                $.ajax({
                    type: "GET",
                    url: "../api/class/fetch?class=" + classID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        // alert(data.message["programName"])
                        $("#edit-class-code").val(data.message["classCode"]);
                        $("#edit-class-program").val(data.message["programCode"]);
                        $("#edit_class").val(data.message["classCode"]);


                    } else {
                        alert(data.message)
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            })
        });

        $("#editClassBtn").on("click", function() {
            $("#editClassForm").submit();
        });

        $("#editClassForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "../api/class/edit",
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

        $("#addNewClassBtn").on("click", function() {
            $("#addNewClassForm").submit();
        });

        $("#addNewClassForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "../api/class/add",
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
                       if (confirm("Are you sure you want to archive this class?")) {
                formData = {
                    "archive-class-code": $(this).attr("id")
                }

                $.ajax({
                    type: "POST",
                    url: "../api/class/archive",
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
    </script>


</body>

</html>