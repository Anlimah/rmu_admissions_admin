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

                <!-- Left side columns -->
                <?php
$classes = new Sections($config["database"]["mysql"]);
$all_classes = $classes->fetchByStaffID($_SESSION["user"]["number"]);
foreach ($all_classes as $class) {
?>
    <div class="col-xxl-3 col-md-12">
        <div class="card info-card sales-card">
            <div class="card-body">
                <h5 class="card-title">Class <?= $class["classCode"] ?></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-contacts-fill"></i>
                    </div>
                    <div class="ps-3">
                        <h6>Course: <?= $class["fk_course"] ?></h6>
                        <p>Credit Hours: <?= $class["credit_hours"] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

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