<?php
session_start();

if (!isset($_GET['status']) || !isset($_GET['exttrid'])) header('Location: index.php?status=invalid');
if (empty($_GET['status']) || empty($_GET['exttrid'])) header('Location: index.php?status=invalid');

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../login.php");
}

if (isset($_SESSION["loginSuccess"]) && $_SESSION["loginSuccess"] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]))
    $trans_id = $_GET["exttrid"];
else header("Location: index.php");

if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "vendors") {
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

    header('Location: ../login.php');
}

require_once('../bootstrap.php');

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

$data = $expose->getApplicationInfo($_GET["exttrid"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head-section.php"); ?>
    <title>Form Purchase | Confirmation</title>
</head>

<body class="fluid-container">

    <div id="wrapper">

        <?php require_once("../inc/page-nav.php"); ?>

        <main class="container flex-container">
            <div class="flex-card">
                <div class="form-card card">

                    <div class="purchase-card-header">
                        <h1>Vendor Portal</h1>
                    </div>

                    <div class="purchase-card-step-info">
                        <span class="step-capsule">Applicant Receipt</span>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <div class="pay-status" style="margin: 0px 5%;" style="align-items: baseline;">
                            <?php if (!empty($data)) { ?>
                                <table style="width:100%;border: 1px solid rgb(155, 155, 155); border-collapse: collapse;" class="mb-4">
                                    <tr>
                                        <td style="width: 120px; background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>VENDOR:</b></td>
                                        <td colspan="2" style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["vendor_name"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PRICE:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["amount"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>APPLICATION NO:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= "RMU-" . $data[0]["app_number"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PIN NO:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["pin_number"] ?></b></td>
                                    </tr>
                                    <tr style="border-top: 1px solid rgb(155, 155, 155)">
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px; padding-top:30px">INSTITUTION:</td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b>REGIONAL MARITIME UNIVERSITY</b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px">FORM NAME:</td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["info"] . "-" . strtoupper($data[0]["name"]) ?></b></td>
                                    </tr>
                                </table>
                                <center>
                                    <button class="btn btn-primary"><b>Print</b></button>
                                </center>
                            <?php } else { ?>
                                <div style="width: 100%;height: 100%; text-align:center">No Data available</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php require_once("../inc/page-footer.php"); ?>
    </div>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {});
    </script>

</body>

</html>