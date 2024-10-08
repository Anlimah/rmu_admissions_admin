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
            <h1>Forms Sale</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">International Form Purchase</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <div id="flashMessage" class="alert text-center" role="alert"></div>

            <div class="row" style="display:flex !important; flex-direction:row !important; justify-content: center !important; align-items: center">
                <div class="flex-card">

                    <div class="form-card card" style="max-width: 600px !important;">

                        <div class="purchase-card-header">
                            <h1>Provide Customer Reference Number</h1>
                        </div>

                        <hr style="color:#999">

                        <div class="purchase-card-body">
                            <form id="RefNumberForm" method="post" enctype="multipart/form-data">
                                <div class="flex-column" style="padding:10px 30px">
                                    <div class="row mb-4">
                                        <div class="col">
                                            <label for="ref_number" class="form-label">Payment Reference Number</label>
                                            <input name="ref_number" id="ref_number" style="text-align:center" class="form-control" type="text" placeholder="Enter reference number" required>
                                        </div>
                                        <div class="col">
                                            <label for="amount" class="form-label">Amount Paid (USD)</label>
                                            <input name="amount" id="amount" style="text-align:center" class="form-control" type="text" placeholder="Enter Amount Oaid" required>
                                        </div>
                                    </div>
                                    <div class="flex-row justify-content-center">
                                        <button class="btn btn-primary btn-sm" type="submit" id="submitBtn" style="padding: 10px 10px; width:200px">Verify</button>
                                    </div>
                                </div>
                                <input type="hidden" name="_FFToken" value="<?= isset($_SESSION["_foreignFormToken"]) ? $_SESSION["_foreignFormToken"] : "" ?>">
                            </form>
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

            $("#RefNumberForm").on("submit", function(e) {
                e.preventDefault();
                triggeredBy = 4;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/ref-number-verify",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            //window.location.href = result.message;
                            window.location.href = "confirm.php?status=000&exttrid=" + result.exttrid;
                        } else {
                            if (result.message == "logout") window.location.href = "?logout=true";
                            else flashMessage("alert-danger", result.message);
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