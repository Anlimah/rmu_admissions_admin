<?php
session_start();
/*
* Designed and programmed by
* @Author: Francis A. Anlimah
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);
die(json_encode($input));*/

require "../bootstrap.php";

use Src\Controller\AdminController;
use Src\Controller\ExcelDataController;

$expose = new AdminController();

$data = [];
$errors = [];

// All GET request will be sent here
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_GET["url"] == "programs") {
        if (isset($_GET["type"])) {
            $t = 0;
            if ($_GET["type"] != "All") {
                $t = (int) $_GET["type"];
            }
            $result = $expose->fetchPrograms($t);
            if (!empty($result)) {
                $data["success"] = true;
                $data["message"] = $result;
            } else {
                $data["success"] = false;
                $data["message"] = "No result found!";
            }
        }
        die(json_encode($data));
    } elseif ($_GET["url"] == "get") {
    }

    // All POST request will be sent here
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    //
    if ($_GET["url"] == "apps-data") {
        if (!isset($_POST["action"]) || !isset($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Invalid input!")));
        if (empty($_POST["action"]) || empty($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Missing request!")));

        $v_action = $expose->validateText($_POST["action"]);
        $v_form_t = $expose->validateNumber($_POST["form_t"]);
        if (!$v_action["success"]) die(json_encode($v_action));
        if (!$v_form_t["success"]) die(json_encode($v_form_t));

        $data = array(
            'action' => $v_action["message"], 'country' => 'All', 'type' => $v_form_t["message"], 'program' => 'All'
        );
        $result = $expose->fetchAppsSummaryData($data);
        if (empty($result)) die(json_encode(array("success" => false, "message" => "Empty result!")));
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "applicants") {

        if (!isset($_POST["action"]) || !isset($_POST["country"]) || !isset($_POST["type"]) || !isset($_POST["program"])) {
            die(json_encode(array("success" => false, "message" => "Missing input!")));
        }
        if (empty($_POST["action"]) || empty($_POST["country"]) || empty($_POST["type"]) || empty($_POST["program"])) {
            die(json_encode(array("success" => false, "message" => "Missing input!")));
        }

        $result = $expose->fetchAppsSummaryData($_POST);
        if (!empty($result)) {
            $data["success"] = true;
            $data["message"] = $result;
        } else {
            $data["success"] = false;
            $data["message"] = "No result found!";
        }
        die(json_encode($data));
    }
    //
    elseif ($_GET["url"] == "getUnadmittedApps") {

        if (!isset($_POST["cert-type"]) || !isset($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"]) || empty($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $result = $expose->fetchAllUnadmittedApplicantsData($_POST["cert-type"], $_POST["prog-type"]);

        if (empty($result)) {
            die(json_encode(array("success" => false, "message" => "No result found!")));
        }
        die(json_encode(array("success" => true, "message" => $result)));

        //
    }
    //
    elseif ($_GET["url"] == "getBroadsheetData") {

        if (!isset($_POST["cert-type"])) die(json_encode(array("success" => false, "message" => "Invalid input field")));
        if (empty($_POST["cert-type"])) die(json_encode(array("success" => false, "message" => "Missing input field")));

        $result = $expose->fetchAllAdmittedApplicantsData($_POST["cert-type"]);

        if (empty($result)) die(json_encode(array("success" => false, "message" => "No result found!")));
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "admitAll") {
        if (!isset($_POST["cert-type"]) || !isset($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"]) || empty($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $result = $expose->admitQualifiedStudents($_POST["cert-type"], $_POST["prog-type"]);

        if (empty($result)) {
            die(json_encode(array("success" => false, "message" => "No result found!")));
        }
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "downloadBS") {
        if (!isset($_POST["cert-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $url = "https://office.rmuictonline.com/download-bs.php?a=bs&c=" . $_POST["cert-type"];
        die(json_encode(array("success" => true, "message" => $url)));
    }
    //
    elseif ($_GET["url"] == "downloadAwaiting") {
        $url = "https://office.rmuictonline.com/download-bs.php?a=as&c=awaiting";
        die(json_encode(array("success" => true, "message" => $url)));
    }
    //
    elseif ($_GET["url"] == "extra-awaiting-data") {

        if (!isset($_POST["action"]) || empty($_POST["action"])) {
            die(json_encode(array("success" => false, "message" => "Invalid request (1)!")));
        }

        $result;

        switch ($_POST["action"]) {
                // download broadsheet dbs
            case 'dbs':
                $broadsheet = new ExcelDataController($_POST['c']);
                $file = $broadsheet->generateFile();
                $result = $broadsheet->downloadFile($file);
                break;

                // upload awaiting datasheet uad
            case 'uad':

                if (!isset($_FILES["awaiting-ds"]) || empty($_FILES["awaiting-ds"])) {
                    die(json_encode(array("success" => false, "message" => "Invalid request!")));
                }

                if ($_FILES["awaiting-ds"]['error']) {
                    die(json_encode(array("success" => false, "message" => "Failed to upload file!")));
                }

                $startRow = $expose->validateNumber($_POST['startRow']);
                if (!$startRow["success"]) die(json_encode($startRow));

                $endRow = $expose->validateNumber($_POST['endRow']);
                if (!$endRow["success"]) die(json_encode($endRow));

                $excelData = new ExcelDataController($_FILES["awaiting-ds"], $_POST['startRow'], $_POST['endRow']);
                $result = $excelData->extractAwaitingApplicantsResults();
                break;
        }

        die(json_encode($result));
    }

    ///
    elseif ($_GET["url"] == "admin-forms-price") {
        if (!isset($_POST["form-type"]) || !isset($_POST["form-price"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["form-type"]) || empty($_POST["form-price"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        
    }


    // All PUT request will be sent here
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    parse_str(file_get_contents("php://input"), $_PUT);
    die(json_encode($data));

    // All DELETE request will be sent here
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);
    die(json_encode($data));
} else {
    http_response_code(405);
}
