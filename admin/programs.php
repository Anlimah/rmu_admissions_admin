<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Core\Base;
use Controller\Programs;

$config = require Base::build_path("config/database.php");

$pageTitle = "Programs";
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
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewProgram">
               ADD
              </button> <div class="modal fade" id="addNewProgram" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add new Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form id="addNewProgForm" class="row g-3" method="POST">
                               
                                <div class="col-md-8">
                                    <label for="add-prog-code" class="form-label">Program Code</label>
                                    <input type="text" class="form-control" id="add-prog-code" name="add-prog-code" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-prog-name" class="form-label">Program Name</label>
                                    <input type="text" class="form-control" id="add-prog-name" name="add-prog-name" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-prog-duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="add-prog-duration" name="add-prog-duration" value="" placeholder="Duration for that program" >
                                </div>
                              
                           
                                <input type="hidden" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>">
                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="add-program" id="addNewProgBtn">Save changes</label>
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
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Programs</h5>
                                    <!-- Bordered Table -->
                                    <table class="table table-borderless datatable">                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Program Code</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Duration</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $programs = new Programs($config["database"]["mysql"]);
                                            $all_programs = $programs->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                            $counter = 1;
                                            foreach ($all_programs as $programs) :
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $counter ?></th>
                                                    <td><?= $programs["programCode"] ?></td>
                                                    <td><?= $programs["programName"] ?></td>
                                                    <td><?= trim($programs["programDuration"] . " " . $programs["durationFormat"]) ?></td>

                                                    <td style="display: flex;">
                                                        <button type="button" class="btn btn-primary btn-sm me-2 editProgramData" data-program="<?= $programs["programCode"] ?>" data-bs-toggle="modal" data-bs-target="#editProgram">Edit</button>
                                                                                                              <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $programs["programCode"] ?>">Archive</button>
                                                        </form>
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
        <div class="modal fade" id="addNewProgram" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add new Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">
                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form id="addNewProgForm" class="row g-3" method="POST">
                               
                                <div class="col-md-8">
                                    <label for="add-prog-code" class="form-label">Program Code</label>
                                    <input type="text" class="form-control" id="add-prog-code" name="add-prog-code" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-prog-name" class="form-label">Program Name</label>
                                    <input type="text" class="form-control" id="add-prog-name" name="add-prog-name" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="add-prog-duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="add-prog-duration" name="add-prog-duration" value="" placeholder="Duration for that program" >
                                </div>
                              
                           
                                <input type="hidden" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>">
                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="add-program" id="addNewProgBtn">Save changes</label>
                    </div>
                </div>
            </div>
        </div><!-- End Full Screen Modal-->

        
        <!-- Edit Lectuer modal -->
        <div class="modal fade" id="editProgram" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="display: grid; place-items: center; height: 100vh; margin: 0;">

                        <div style="display: flex; width:600px">
                            <!-- Multi Columns Form -->
                            <form class="row g-3" method="POST"  id="editProgForm">
                             
                                        
                            <div class="col-md-8">
                                    <label for="edit-prog-code" class="form-label">Program Code</label>
                                    <input type="text" class="form-control" id="edit-prog-code" name="edit-prog-code" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="edit-prog-name" class="form-label">Program Name</label>
                                    <input type="text" class="form-control" id="edit-prog-name" name="edit-prog-name" value="">
                                </div>
                                <div class="col-md-8">
                                    <label for="edit-prog-duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="edit-prog-duration" name="edit-prog-duration" value="" placeholder="Duration for that program" >
                                </div>
                                 <div class="col-md-8">
                                    <label for="edit-prog-format" class="form-label">Format</label>
                                    <input type="text" class="form-control" id="edit-prog-format" name="edit-prog-format" value="" >
                                <input type="hidden" name="edit-prog-number" id="edit-prog-number" value="">
                                <input type="hidden" name="department" value="<?= $_SESSION["user"]["fk_department"] ?>">

                            </form><!-- End Multi Columns Form -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="btn btn-secondary" data-bs-dismiss="modal">Close</label>
                        <label class="btn btn-primary" for="edit-program" id="editProgBtn">Save changes</label>
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

            $(".editProgramData").on("click", function() {
                progID = this.dataset.program;

                $.ajax({
                    type: "GET",
                    url: "../api/program/fetch?program=" + progID,
                }).done(function(data) {
                    console.log(data);
                    if (data.success) {
                        $("#edit-prog-name").val(data.message["programName"]);
                        $("#edit-prog-code").val(data.message["programCode"]);
                        $("#edit-prog-duration").val(data.message["programDuration"]);
                        $("#edit-prog-number").val(data.message["programCode"]);
                        $("#edit-prog-format").val(data.message["durationFormat"]);

                        
                    } else {
                        alert(data.message)
                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            $("#addNewProgBtn").on("click", function() {
                $("#addNewProgForm").submit();
            });

            $("#addNewProgForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/program/add",
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

            $("#editProgBtn").on("click", function() {
                $("#editProgForm").submit();
            });

            $("#editProgForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/program/edit",
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
                        "archive-prog": $(this).attr("id")
                    }

                    $.ajax({
                        type: "POST",
                        url: "../api/program/archive",
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