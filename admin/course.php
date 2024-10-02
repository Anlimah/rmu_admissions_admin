<?php
session_start();
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Controller\Sections;
use Core\Base;

$config = require Base::build_path("config/database.php");
$counts = new Sections($config["database"]["mysql"]);

$pageTitle = "Courses";
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
           <!-- Left side columns -->
                <div class="col-lg-12">
                

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Courses</h5>

                                    <!-- Borderless Table -->
                                    <table class="table table-borderless datatable">
                                        <thead>
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
                                            $classes = new Sections($config["database"]["mysql"]);
                                            $all_classes = $classes->fetchByStaffID($_SESSION["user"]["number"]);
                                            $rowCounter = 1;
                                            foreach ($all_classes as $classes) {
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $rowCounter ?></th>
                                                    <td><?= $classes["fk_course"] ?></td>
                                                    <td><?= $classes["name"] ?></td>
                                                    <td><?= $classes["credit_hours"] ?></td>
                                                    <!-- <td style="display: flex;">
                                                        <button type="button" class="btn btn-primary btn-sm me-2 editClassData" data-class="<?= $classes["classCode"] ?>" data-bs-toggle="modal" data-bs-target="#editClass">Edit</button>
                                                        <button type="button" class="btn btn-danger btn-sm archiveBtn" id="<?= $classes["classCode"] ?>">Archive</button>
                                                    </td> -->
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
                    $("#edit-class-code").val(data.message["classCode"]);
                    $("#edit-class-program").val(data.message["programCode"]);
                    $("#edit_class").val(data.message["classCode"]);
                } else {
                    alert(data.message)
                }
            }).fail(function(err) {
                console.log(err);
            });
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

    });
</script>



</body>

</html>