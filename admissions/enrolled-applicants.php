<?php
session_start();

if (!isset($_SESSION["adminLogSuccess"]) || $_SESSION["adminLogSuccess"] == false || !isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    header("Location: ../index.php");
}

$isUser = false;
if (strtolower($_SESSION["role"]) == "admissions" || strtolower($_SESSION["role"]) == "developers") $isUser = true;

if (isset($_GET['logout']) || !$isUser) {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: ../index.php');
}

$_SESSION["lastAccessed"] = time();

require_once('../bootstrap.php');

use Src\Controller\AdminController;

require_once('../inc/admin-database-con.php');

$admin = new AdminController($db, $user, $pass);
require_once('../inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= require_once("../inc/head.php") ?>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Enrolled Applicants</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Enrolled Applicants</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Recent Sales -->
                <div class="col-12">

                    <div class="card recent-sales overflow-auto">

                        <div class="filter">
                            <span id="dbs-progress"></span>
                            <a class="icon" id="download-bs" href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download Broadsheets">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Enrolled Applicants</h5>
                            <form id="fetchDataForm" class="mb-4">
                                <div class="row" style="justify-content: baseline; align-items: center">
                                    <div class="col-3">
                                        <select name="cert-type" id="cert-type" data-id="cert" class="form-select">
                                            <option value="" hidden>Choose Category</option>
                                            <option value="MASTERS">MASTERS</option>
                                            <option value="DEGREE">DEGREE</option>
                                            <option value="DIPLOMA">DIPLOMA</option>
                                            <option value="UPGRADE">UPGRADERS</option>
                                            <option value="SHORT">SHORT/OTHER COURSES</option>
                                        </select>
                                    </div>
                                    <div class="col-3" id="bs-masters-prog">
                                        <select name="prog-type" id="prog-type" data-id="prog" class="form-select">
                                            <option value="" hidden>Choose Program</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <div id="info-output"></div>
                            <table class="table table-borderless datatable table-striped table-hover">
                                <thead>
                                    <tr class="table-dark">
                                        <th scope="col">#</th>
                                        <th scope="col">Index No.</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Programme</th>
                                        <th scope="col">Application Term</th>
                                        <th scope="col">Study Stream</th>
                                        <th scope="col"> </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div><!-- End Recent Sales -->

            </div>
        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>

    <script>
        $(document).ready(function() {

            var data;

            var fetchPrograms = function(data) {
                $.ajax({
                    type: "GET",
                    url: "../endpoint/programsByCategory",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("#prog-type").html('<option value="" hidden>Choose Programme</option>');
                            $.each(result.message, function(index, value) {
                                $("#prog-type").append(
                                    '<option value="' + value.id + '">' + value.name + '</option>'
                                );
                            });
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            var fetchBroadsheet = function(data) {
                console.log(data);

                $.ajax({
                    type: "POST",
                    url: "../endpoint/getAllEnrolledApplicants",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                $("tbody").append(
                                    '<tr>' +
                                    '<th scope="row">' + (index + 1) + '</th>' +
                                    '<td>' + value.index_number + '</td>' +
                                    '<td>' + value.full_name + '</td>' +
                                    '<td>' + value.program_name + '</td>' +
                                    '<td>' + value.term_admitted + '</td>' +
                                    '<td>' + value.stream_admitted + '</td>' +
                                    '<td><b><a href="applicant-info.php?t=' + value.program_type + '&c=' + data["cert-type"] + '&q=' + value.fk_applicant + '">Open</a></b></td>' +
                                    '</tr>'
                                );
                            });
                            $("#info-output").hide();

                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            $("tbody").html("<tr style='text-align: center'><td colspan='7'>No entries found</td></tr>");
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            let triggeredBy = 0;

            $("#cert-type, #prog-type").change("blur", function() {
                data = {
                    "cert-type": $("#cert-type").val()
                };
                if (this.dataset.id === "cert") {
                    fetchPrograms(data);
                    data["prog-type"] = "";
                }
                if (this.dataset.id === "prog") data["prog-type"] = $("#prog-type").val();

                fetchBroadsheet(data);
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 1) $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> sending...');
                    if (triggeredBy == 2) $("#admit-all-bs").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 1) $("#submitBtn").prop("disabled", false).html('Fetch Data');
                    if (triggeredBy == 2) $("#admit-all-bs").prop("disabled", false).html('Admit All Qualified');
                }
            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
</body>

</html>