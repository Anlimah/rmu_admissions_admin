<?php
session_start();

if (!isset($_SESSION["adminLogSuccess"]) || $_SESSION["adminLogSuccess"] == false || !isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    header("Location: ../index.php");
}
if (!isset($_SESSION["vendor_id"]) || empty($_SESSION["vendor_id"])) header("Location: index.php?msg=Access denied!");

$isUser = false;
if (strtolower($_SESSION["role"]) == "vendors" || strtolower($_SESSION["role"]) == "developers") $isUser = true;

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

if (!isset($_SESSION["_foreignFormToken"])) {
    $rstrong = true;
    $_SESSION["_foreignFormToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
    $_SESSION["vendor_type"] = "VENDOR";
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
    <style>
        .hide {
            display: none;
        }

        .display {
            display: block;
        }

        #wrapper {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 100% !important;
            height: 100% !important;
        }

        .flex-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .flex-container>div {
            height: 100% !important;
            width: 100% !important;
        }

        .flex-column {
            display: flex !important;
            flex-direction: column !important;
        }

        .flex-row {
            display: flex !important;
            flex-direction: row !important;
        }

        .justify-center {
            justify-content: center !important;
        }

        .justify-space-between {
            justify-content: space-between !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .align-items-baseline {
            align-items: baseline !important;
        }

        .flex-card {
            display: flex !important;
            justify-content: center !important;
            flex-direction: row !important;
        }

        .form-card {
            height: 100% !important;
            max-width: 425px !important;
            padding: 15px 10px 20px 10px !important;
        }

        .flex-card>.form-card {
            height: 100% !important;
            width: 100% !important;
        }

        .purchase-card-header {
            padding: 0 !important;
            width: 100% !important;
            height: 40px !important;
        }

        .purchase-card-header>h1 {
            font-size: 22px !important;
            font-weight: 600 !important;
            color: #003262 !important;
            text-align: center;
            width: 100%;
        }

        .purchase-card-step-info {
            color: #003262;
            padding: 0px;
            font-size: 14px;
            font-weight: 400;
            width: 100%;
        }

        .purchase-card-footer {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>International Purchase</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">International Purchase</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <div class="row">

                <div class="col-lg-12">
                    <div class="row">

                        <?php //var_dump($admin->fetchTotalAppsByProgCodeAndAdmisPeriod('MSC', 0)[0]["total"]) 
                        ?>
                        <!-- Applications Card -->
                        <div class="col-xxl-4 col-md-4">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <a href="#">
                                        <h5 class="card-title">Pending</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-receipt-pending-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <h6><?= $admin->fetchTotalInternationalFormPurchaseRequestsByStatus('pending')[0]["total"]; ?></h6>
                                                <span class="text-muted small pt-2 ps-1">Requests</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-4 col-md-4">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <a href="#">
                                        <h5 class="card-title">Approved</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-receipt-approved-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <h6><?= $admin->fetchTotalInternationalFormPurchaseRequestsByStatus('approved')[0]["total"]; ?></h6>
                                                <span class="text-muted small pt-2 ps-1">Requests</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-4 col-md-4">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <a href="#">
                                        <h5 class="card-title">Declined</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-receipt-declined-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <h6><?= $admin->fetchTotalInternationalFormPurchaseRequestsByStatus('declined')[0]["total"]; ?></h6>
                                                <span class="text-muted small pt-2 ps-1">Requests</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                    </div>
                </div><!-- Forms Sales Card  -->
            </div>

        </section>

        <section class="section dashboard">
            <div class="row">
                <div class="col-12">

                    <div class="card recent-sales overflow-auto">

                        <div class="card-body">
                            <h5 class="card-title">Requests</h5>

                            <div>
                                <table class="table table-borderless datatable table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col" style="width:150px">Applicant Name</th>
                                            <th scope="col">Contact</th>
                                            <th scope="col">Membership</th>
                                            <th scope="col">Ref. Number</th>
                                            <th scope="col">Form Type</th>
                                            <th scope="col">Form Price</th>
                                            <th scope="col">Actions</th>
                                            <!-- <th scope="col"></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $purchaseData = $admin->fetchAllInternationalFormPurchaseRequestsByStatus('pending');
                                        if (!empty($purchaseData)) {
                                            $index = 1;
                                            foreach ($purchaseData as $pd) {

                                                preg_match('/RMUF(\d)/', $pd["reference_number"], $matches);
                                                $membership = $matches[1] ? "Member" : "Non-Member";
                                        ?>
                                                <tr>
                                                    <td><?= $index ?></td>
                                                    <td><?= $pd["first_name"] . " " . $pd["last_name"] ?></td>
                                                    <td><span><?= "(" . $pd["p_country_code"] . ") " ?></span><?= $pd["phone_number"] ?></td>
                                                    <td><?= $membership ?></td>
                                                    <td><strong><?= $pd["reference_number"] ?></strong></td>
                                                    <td><?= $pd["form_type"] ?></td>
                                                    <td><?= $pd["form_price"] ?></td>
                                                    <td>
                                                        <form action="#" method="post" id="sfd">
                                                            <input type="hidden" name="membership" value="<?= $membership ?>">
                                                            <input type="hidden" name="ref-number" value="<?= $pd["reference_number"] ?>">
                                                            <input type="hidden" name="_FFToken" value="<?= $_SESSION["_foreignFormToken"] ?>">
                                                            <input type="hidden" name="action" value="" id="action-<?= $pd["reference_number"] ?>">
                                                            <button class="btn btn-success btn-xs approve-btn" id="<?= $pd["reference_number"] ?>">Approve</button>
                                                            <button class="btn btn-danger btn-xs decline-btn" id="<?= $pd["reference_number"] ?>">Decline</button>
                                                        </form>
                                                    </td>
                                                    <!-- <td>
                                                        <button class="btn btn-primary btn-xs rounded-pill more-btn" id="<?= $pd["reference_number"] ?>">More</button>
                                                    </td> -->
                                                </tr>
                                        <?php
                                                $index++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $(document).ready(function() {

            $(".form-select").change("blur", function() {
                $.ajax({
                    type: "POST",
                    url: "../endpoint/formInfo",
                    data: {
                        form_id: this.value,
                    },
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            $("#form-cost-display").show();
                            $("#form-name").text(result.message[0]["name"]);
                            $("#form-cost").text(result.message[0]["amount"]);
                            $("#form_price").val(result.message[0]["amount"]);
                            //$("#form_type").val(result.message[0]["form_type"]);
                            $(':input[type="submit"]').prop('disabled', false);
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                        }
                    },
                    error: function(error) {
                        console.log(error.statusText);
                    }
                });
            });

            var formAction;

            $(".approve-btn").on("click", function(e) {
                formAction = 'approve';
                action = "action-" + $(this).attr("id");
                document.getElementById(action).value = 'approve';
                $("#sfd" + $(this).attr("id")).submit();
            });

            $(".decline-btn").on("click", function(e) {
                formAction = 'decline';
                action = "action-" + $(this).attr("id");
                document.getElementById(action).value = 'decline';
                $("#sfd" + $(this).attr("id")).submit();
            });

            $("form").on("submit", function(e) {

                e.preventDefault();

                if (this.action.value === 'approve') {
                    var c = confirm("Please verify that the required form price has been paid by applicant. Are you sure you want to approve this transaction?")
                    if (!c) return;
                }

                formData = new FormData(this);
                triggeredBy = 4;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/ref-number-verify",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            if (formAction === 'approve')
                                window.location.href = "confirm.php?status=000&exttrid=" + result.exttrid;
                            if (formAction === 'decline') {
                                alert(result.message);
                                document.location.reload();
                            }
                        } else {
                            if (result.message == "logout") {
                                alert('Your session expired. Please refresh the page to continue!');
                                window.location.href = "?logout=true";
                            } else flashMessage("alert-danger", result.message);
                            //console.log("success area: ", result.message);
                        }
                    },
                    error: function(error) {
                        console.log("error area: ", error);
                        flashMessage("alert-danger", error);
                    }
                });
            });

            $("#num1").focus();

            $(".num").on("keyup", function() {
                if (this.value.length == 4) {
                    $(this).next(":input").focus().select(); //.val(''); and as well clesr
                }
            });

            $("input[type='text']").on("click", function() {
                $(this).select();
            });

            function flashMessage(bg_color, message) {
                const flashMessage = document.getElementById("flashMessage");

                flashMessage.classList.add(bg_color);
                flashMessage.innerHTML = message;

                setTimeout(() => {
                    flashMessage.style.visibility = "visible";
                    flashMessage.classList.add("show");
                }, 1000);

                setTimeout(() => {
                    flashMessage.classList.remove("show");
                    setTimeout(() => {
                        flashMessage.style.visibility = "hidden";
                    }, 5000);
                }, 5000);
            }
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