<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;
use Src\Controller\ExposeDataController;
use Src\Controller\PaymentController;

class AdminController
{
    private $dm = null;
    private $expose = null;

    public function __construct()
    {
        $this->dm = new DatabaseMethods();
        $this->expose = new ExposeDataController();
    }

    public function processVendorPay($data)
    {
        $payConfirm = new PaymentController();
        return $payConfirm->vendorPaymentProcess($data);
    }

    public function verifyAdminLogin($username, $password)
    {
        $sql = "SELECT * FROM `sys_users` WHERE `user_name` = :u";
        $data = $this->dm->getData($sql, array(':u' => $username));
        if (!empty($data)) {
            if (password_verify($password, $data[0]["password"])) {
                return $data;
            }
        }
        return 0;
    }

    public function getAcademicPeriod()
    {
        $query = "SELECT YEAR(`start_date`) AS start_year, YEAR(`end_date`) AS end_year, info 
                FROM admission_period WHERE active = 1";
        return $this->dm->getData($query);
    }

    public function getCurrentAdmissionPeriodID()
    {
        return $this->dm->getID("SELECT `id` FROM `admission_period` WHERE `active` = 1");
    }

    public function fetchPrograms(int $type)
    {
        $param = array();
        if ($type != 0) {
            $query = "SELECT * FROM programs WHERE `type` = :t";
            $param = array(':t' => $type);
        } else {
            $query = "SELECT * FROM programs";
        }
        return $this->dm->getData($query, $param);
    }

    public function getFormTypes()
    {
        return $this->dm->getData("SELECT * FROM `form_type`");
    }

    public function fetchUserName($user_id)
    {
        $sql = "SELECT CONCAT(SUBSTRING(`first_name`, 1, 1), '. ' , `last_name`) AS `userName` 
                FROM `sys_users` WHERE `id` = :u";
        return $this->dm->getData($sql, array(':u' => $user_id));
    }

    public function fetchFullName($user_id)
    {
        $sql = "SELECT CONCAT(`first_name`, ' ' , `last_name`) AS `fullName`, 
                user_name AS email_address, `role` AS user_role 
                FROM `sys_users` WHERE `id` = :u";
        return $this->dm->getData($sql, array(':u' => $user_id));
    }

    public function logActivity(int $user_id, $operation, $description)
    {
        $query = "INSERT INTO `activity_logs`(`user_id`, `operation`, `description`) VALUES (:u,:o,:d)";
        $params = array(":u" => $user_id, ":o" => $operation, ":d" => $description);
        $this->dm->inputData($query, $params);
    }
    // For admin settings


    /**
     * CRUD for form price
     */

    public function fetchAllFormPriceDetails()
    {
        $query = "SELECT fp.id, ft.name, fp.amount, fp.admin_period 
                FROM form_type AS ft, form_price AS fp, admission_period AS ap 
                WHERE ft.id = fp.form_type AND ap.id = fp.admin_period AND ap.active = 1";
        return $this->dm->getData($query);
    }

    public function fetchFormPrice($form_price_id)
    {
        $query = "SELECT fp.id AS fp_id, fp.id AS ft_id, ft.name, fp.amount, fp.admin_period 
                FROM form_type AS ft, form_price AS fp, admission_period AS ap 
                WHERE ft.id = fp.form_type AND ap.id = fp.admin_period AND 
                ap.active = 1 AND fp.id = :i";
        return $this->dm->getData($query, array(":i" => $form_price_id));
    }

    public function addFormPrice($form_type, $form_price)
    {
        $query = "INSERT INTO form_price (form_type, admin_period, amount) VALUES(:ft, :ap, :a)";
        $aca_period = $this->getCurrentAdmissionPeriodID();
        $params = array(":ft" => $form_type, ":ap" => $aca_period, ":a" => $form_price);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added new form price of {$form_price} to form type {$form_type} for academic period {$aca_period}"
            );
        return $query_result;
    }

    public function updateFormPrice($form_type, $form_price)
    {
        $query = "UPDATE form_price SET amount = :a WHERE form_type = :ft AND admin_period = :ap";
        $aca_period = $this->getCurrentAdmissionPeriodID();
        $params = array(":ft" => $form_type, ":ap" => $aca_period, ":a" => $form_price);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated form price to {$form_price} for form type {$form_type} on academic period {$aca_period}"
            );
        return $query_result;
    }

    public function deleteFormPrice($form_price_id)
    {
        $query = "DELETE FROM form_price WHERE id = :i";
        $params = array(":i" => $form_price_id);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted form price with id {$form_price_id}"
            );
        return $query_result;
    }

    /**
     * CRUD for vendor
     */

    public function fetchAllVendorDetails()
    {
        return $this->dm->getData("SELECT * FROM vendor_details WHERE `type` <> 'ONLINE'");
    }

    public function fetchVendor($vendor_id)
    {
        $query = "SELECT * FROM vendor_details WHERE id = :i";
        return $this->dm->inputData($query, array(":i" => $vendor_id));
    }

    public function addVendor($v_name, $v_tin, $v_email, $v_phone, $v_address)
    {
        $query1 = "INSERT INTO vendor_details (`id`, `type`, `vendor_name`, `tin`, `email_address`, `phone_number`, `address`) 
                VALUES(:id, :tp, :nm, :tn, :ea, :pn, :ad)";
        $vendor_id = time();
        $params1 = array(
            ":id" => $vendor_id, ":tp" => "VENDOR", ":nm" => $v_name, ":tn" => $v_tin,
            ":ea" => $v_email, ":pn" => $v_phone, ":ad" => $v_address
        );

        if ($this->dm->inputData($query1, $params1)) {

            $password = $this->expose->genVendorPin();
            $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

            $query2 = "INSERT INTO vendor_login (`user_name`, `password`, `vendor`) VALUES(:un, :pw, :vi)";
            $params2 = array(":un" => sha1($v_email), ":pw" => $hashed_pw, ":vi" => $vendor_id);
            $query_result = $this->dm->inputData($query2, $params2);

            if ($query_result)
                $this->logActivity(
                    $_SESSION["user"],
                    "INSERT",
                    "Added vendor {$vendor_id} with username/email {$v_email}"
                );

            $subject = "RMU Vendor Registration";
            $message = "<p>Hi," . $v_name . " </p></br>";
            $message .= "<p>Find below your Login details.</p></br>";
            $message .= "<p style='font-weight: bold;'>Username: " . $v_email . "</p>";
            $message .= "<p style='font-weight: bold;'>Password: " . $password . "</p></br>";
            $message .= "<div>Please note the following: </div>";
            $message .= "<ol style='color:red; font-weight:bold;'>";
            $message .= "<li>Don't let anyone see your login password</li>";
            $message .= "<li>Access the portal and change your password</li>";
            $message .= "</ol></br>";
            $message .= "<p><a href='forms.rmuictonline.com/buy-vendor/'>Click here</a> to access portal.</ol>";

            return $this->expose->sendEmail($v_email, $subject, $message);
        }
        return 0;
    }

    public function updateVendor($v_id, $v_name, $v_tin, $v_email, $v_phone, $v_address)
    {
        $query = "UPDATE vendor_details SET `vendor_name` = :nm, `tin` = :tn, 
                `email_address` = :ea, `phone_number` = :pn, `address` = :ad
                WHERE id = :id";
        $params = array(
            ":id" => $v_id, ":nm" => $v_name, ":tn" => $v_tin,
            ":ea" => $v_email, ":pn" => $v_phone, ":ad" => $v_address
        );
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated information for vendor {$v_id}"
            );
        return $query_result;
    }

    public function deleteVendor($vendor_id)
    {
        $query = "DELETE FROM vendor_details WHERE id = :i";
        $params = array(":i" => $vendor_id);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted vendor {$vendor_id} information"
            );
        return $query_result;
    }

    /**
     * CRUD for programme
     */

    public function fetchAllPrograms()
    {
        $query = "SELECT p.`id`, p.`name`, f.name AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, form_type AS f WHERE p.type = f.id";
        return $this->dm->getData($query);
    }

    public function fetchProgramme($prog_id)
    {
        $query = "SELECT p.`id`, p.`name`, f.id AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, form_type AS f WHERE p.type = f.id AND p.id = :i";
        return $this->dm->getData($query, array(":i" => $prog_id));
    }

    public function addProgramme($prog_name, $prog_type, $prog_wkd, $prog_grp)
    {
        $query = "INSERT INTO programs (`name`, `type`, `weekend`, `group`) VALUES(:n, :t, :w, :g)";
        $params = array(":n" => strtoupper($prog_name), ":t" => $prog_type, ":w" => $prog_wkd, ":g" => $prog_grp);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added new programme {$prog_name} of programme type {$prog_type}"
            );
        return $query_result;
    }

    public function updateProgramme($prog_id, $prog_name, $prog_type, $prog_wkd, $prog_grp)
    {
        $query = "UPDATE programs SET `name` = :n, `type` = :t, `weekend` = :w, `group` = :g WHERE id = :i";
        $params = array(":n" => strtoupper($prog_name), ":t" => $prog_type, ":w" => $prog_wkd, ":g" => $prog_grp, ":i" => $prog_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated information for program {$prog_id}"
            );
        return $query_result;
    }

    public function deleteProgramme($prog_id)
    {
        $query = "DELETE FROM programs WHERE id = :i";
        $query_result = $this->dm->inputData($query, array(":i" => $prog_id));
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted programme {$prog_id}"
            );
        return $query_result;
    }

    /**
     * CRUD for Admission Period
     */

    public function fetchAllAdmissionPeriod()
    {
        return $this->dm->getData("SELECT * FROM admission_period ORDER BY `end_date` ASC");
    }

    public function fetchCurrentAdmissionPeriod()
    {
        return $this->dm->getData("SELECT * FROM admission_period WHERE `active` = 1");
    }

    public function fetchAdmissionPeriod($adp_id)
    {
        $query = "SELECT * FROM admission_period WHERE id = :i";
        return $this->dm->inputData($query, array(":i" => $adp_id));
    }

    public function addAdmissionPeriod($adp_start, $adp_end, $adp_info)
    {
        if ($this->fetchCurrentAdmissionPeriod()) {
            return array("success" => false, "message" => "Can't open admission period! One already open.");
        }
        $query = "INSERT INTO admission_period (`start_date`, `end_date`, `info`, `active`) 
                VALUES(:sd, :ed, :i, :a)";
        $params = array(":sd" => $adp_start, ":ed" => $adp_end, ":i" => $adp_info, ":a" => 1);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result) {
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added admisiion period  with start date {$adp_start} and end date {$adp_end}"
            );

            return array("success" => true, "message" => "Successfully open new admission period!");
        }
        return array("success" => false, "message" => "Failed to open new admission period!");
    }

    public function updateAdmissionPeriod($adp_id, $adp_end, $adp_info)
    {
        $query = "UPDATE admission_period SET `end_date` = :ed, `info` = :i WHERE id = :id";
        $params = array(":ed" => $adp_end, ":i" => $adp_info, ":id" => $adp_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated information for admisiion period {$adp_id}"
            );
        return $query_result;
    }

    public function closeAdmissionPeriod($adp_id)
    {
        $query = "UPDATE admission_period SET active = 0 WHERE id = :i";
        $query_result = $this->dm->inputData($query, array(":i" => $adp_id));
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Closed admission with id {$adp_id}"
            );
        return $query_result;
    }


    /**
     * CRUD for user accounts
     */

    public function fetchAllSystemUsers()
    {
        return $this->dm->getData("SELECT * FROM sys_users");
    }

    public function fetchSystemUser($user_id)
    {
        $query = "SELECT u.*, p.`select`, p.`insert`, p.`update`, p.`delete` 
                FROM sys_users AS u, sys_users_privileges AS p 
                WHERE u.`id` = :i AND u.`id` = p.`user_id`";
        return $this->dm->inputData($query, array(":i" => $user_id));
    }

    public function addSystemUser($user_data, $privileges)
    {
        //Add users Details to sys_users table
        // Generate password
        $password = $this->expose->genVendorPin();
        // Hash password
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        // Create insert query
        $query1 = "INSERT INTO sys_users (`first_name`, `last_name`, `user_name`, `password`, `role`) VALUES(:fn, :ln, :un, :pw, :rl)";
        $params1 = array(
            ":fn" => $user_data["first_name"], ":ln" => $user_data["last_name"], ":un" => $user_data["user_name"],
            ":pw" => $hashed_pw, ":rl" => $user_data["user_role"]
        );
        // execute query
        $action1 = $this->dm->inputData($query1, $params1);
        if (!$action1) return array("success" => false, "message" => "Failed to create user account!");
        // verify and get user account info
        $sys_user = $this->verifyAdminLogin($user_data["user_name"], $password);

        if (empty($sys_user)) return array("success" => false, "message" => "Created user account, but failed to verify user account!");
        // Create insert query for user privileges
        $query2 = "INSERT INTO `sys_users_privileges` (`user_id`, `select`,`insert`,`update`,`delete`) 
                VALUES(:ui, :s, :i, :u, :d)";
        $params2 = array(
            ":ui" => $sys_user[0]["id"], ":s" => $privileges["select"], ":i" => $privileges["insert"],
            ":u" => $privileges["update"], ":d" => $privileges["delete"]
        );
        // Execute user privileges 
        $action2 = $this->dm->inputData($query2, $params2);
        if (!$action2) return array("success" => false, "message" => "Failed to create given roles for the user!");

        $subject = "RMU - Account User";

        if ($user_data["user_role"] == "Vendors") {
            $query1 = "INSERT INTO vendor_details (`id`, `type`, `tin`, `phone_number`, `company`, `address`, `user_id`) 
                    VALUES(:id, :tp, :tn, :pn, :cp, :ad, :ui)";
            $vendor_id = time();
            $params1 = array(
                ":id" => $vendor_id, ":tp" => "VENDOR", ":tn" => $user_data["vendor_tin"], ":pn" => $user_data["vendor_phone"],
                ":cp" => $user_data["vendor_company"], ":ad" => $user_data["vendor_address"], ":ui" => $sys_user[0]["id"]
            );
            $this->dm->inputData($query1, $params1);
            $subject = "RMU - Vendor Account";
        }

        $this->logActivity(
            $_SESSION["user"],
            "INSERT",
            "Added new user account with username/email {$user_data["user_name"]}"
        );

        // Prepare email
        $message = "<p>Hi " . $user_data["first_name"] . ", </p></br>";
        $message .= "<p>Find below your Login details.</p></br>";
        $message .= "<p style='font-weight: bold;'>Username: " . $user_data["user_name"] . "</p>";
        $message .= "<p style='font-weight: bold;'>Password: " . $password . "</p></br>";
        $message .= "<div>Please note the following: </div>";
        $message .= "<ol style='color:red; font-weight:bold;'>";
        $message .= "<li>Don't let anyone see your login password</li>";
        $message .= "<li>Access the portal and change your password</li>";
        $message .= "</ol></br>";
        $message .= "<p><a href='office.rmuictonline.com'>Click here</a> to access portal.</ol>";

        // Send email
        $emailed = $this->expose->sendEmail($user_data["user_name"], $subject, $message);
        // verify email status and return result
        if ($emailed !== 1) return array(
            "success" => false,
            "message" => "Created user account, but failed to send email! Error: " . $emailed
        );

        return array("success" => true, "message" => "Successfully created user account!");
    }

    public function updateSystemUser($user_id, $first_name, $last_name, $email_addr, $role, $privileges)
    {
        $query = "UPDATE sys_users SET 
                `user_name` = :un, `first_name` = :fn, `last_name` = :ln, `role` = :rl 
                WHERE id = :id";
        $params = array(
            ":un" => $email_addr, ":fn" => $first_name, ":ln" => $last_name,
            ":rl" => $role, ":id" => $user_id
        );
        if ($this->dm->inputData($query, $params)) {
            // Create insert query for user privileges
            $query2 = "UPDATE `sys_users_privileges` SET `select` = :s, `insert` = :i,`update` = :u, `delete`= :d 
                        WHERE `user_id` = :ui";
            $params2 = array(
                ":ui" => $user_id, ":s" => $privileges["select"], ":i" => $privileges["insert"],
                ":u" => $privileges["update"], ":d" => $privileges["delete"]
            );
            // Execute user privileges 
            $action2 = $this->dm->inputData($query2, $params2);
            if (!$action2) return array("success" => false, "message" => "Failed to update user account privileges!");

            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated user {$user_id} account information and privileges"
            );

            return array("success" => true, "message" => "Successfully updated user account information!");
        }
        return array("success" => false, "message" => "Failed to update user account information!");
    }

    public function changeSystemUserPassword($user_id, $email_addr, $first_name)
    {
        $password = $this->expose->genVendorPin();
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE sys_users SET `password` = :pw WHERE id = :id";
        $params = array(":id" => $user_id, ":pw" => $hashed_pw);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result) {

            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated user {$user_id} account's password"
            );

            return $query_result;
            $subject = "RMU System User";
            $message = "<p>Hi " . $first_name . ", </p></br>";
            $message .= "<p>Find below your Login details.</p></br>";
            $message .= "<p style='font-weight: bold; font-size: 18px'>Username: " . $email_addr . "</p></br>";
            $message .= "<p style='font-weight: bold; font-size: 18px'>Password: " . $password . "</p></br>";
            $message .= "<p style='color:red; font-weight:bold'>Don't let anyone see your login password</p></br>";
            return $this->expose->sendEmail($email_addr, $subject, $message);
        }
        return 0;
    }

    public function deleteSystemUser($user_id)
    {
        $query = "DELETE FROM sys_users WHERE id = :i";
        $params = array(":i" => $user_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Removed user {$user_id} accounts"
            );
        return $query_result;
    }

    // end of setups

    public function fetchAvailableformTypes()
    {
        return $this->dm->getData("SELECT * FROM form_type");
    }

    public function getFormTypeName(int $form_type)
    {
        $query = "SELECT * FROM form_type WHERE id = :i";
        return $this->dm->getData($query, array(":i" => $form_type));
    }

    public function fetchAllAwaitingApplicationsBS()
    {
        $query = "SELECT pd.id AS AdmissionNumber, ab.index_number AS IndexNumber, 
                    ab.month_completed AS ExamMonth, ab.year_completed AS ExamYear 
                FROM 
                    applicants_login AS al, purchase_detail AS pd, 
                    admission_period AS ap, academic_background AS ab  
                WHERE
                    al.id = ab.app_login AND al.purchase_id = pd.id AND 
                    ap.id = pd.admission_period AND ab.awaiting_result = 1 AND ap.active = 1";
        return $this->dm->getData($query);
    }

    /**
     * Fetching forms sale data totals
     */

    public function fetchTotalFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1";
        return $this->dm->getData($query);
    }

    public function fetchTotalPostgradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND ft.name LIKE '%Post%' OR ft.name LIKE '%Master%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalUdergradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND (ft.name LIKE '%Degree%' OR ft.name LIKE '%Diploma%')";
        return $this->dm->getData($query);
    }

    public function fetchTotalShortCoursesFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND ft.name LIKE '%Short%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalVendorsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND v.vendor_name NOT LIKE '%ONLINE%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalOnlineFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, form_type AS ft, form_price AS fp, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_type = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
            AND v.vendor_name LIKE '%ONLINE%'";
        return $this->dm->getData($query);
    }

    /**
     * Fetching form sales data by statistics
     */
    public function fetchFormsSoldStatsByVendor()
    {
        $query = "SELECT 
                    v.vendor_name, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.vendor";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByPaymentMethod()
    {
        $query = "SELECT 
                    pd.payment_method, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.payment_method";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByFormType()
    {
        $query = "SELECT 
                    ft.name, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.form_type";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByCountry()
    {
        $query = "SELECT 
                    pd.country_name, pd.country_code, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.country_code";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByPurchaseStatus()
    {
        $query = "SELECT 
                    pd.status, COUNT(pd.id) AS total, SUM(fp.amount) AS amount 
                FROM 
                    purchase_detail AS pd, form_type AS ft, form_price AS fp, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_type = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND fp.form_type = ft.id AND ap.active = 1 
                GROUP BY pd.status";
        return $this->dm->getData($query);
    }

    /**
     * fetching applicants data
     */

    public function fetchAppsSummaryData($data)
    {
        // extract the array values into variables
        // create a new array with the keys of $data as the values and the values of $data as the keys
        // and then extract the values of the new array into variables
        extract(array_combine(array_keys($data), array_values($data)));

        $SQL_COND = "";
        if ($country != "All") $SQL_COND .= " AND p.`nationality` = '$country'";
        if ($type != "All") $SQL_COND .= " AND ft.`id` = $type";
        if ($program != "All") $SQL_COND .= " AND pi.`first_prog` = '$program' OR pi.`second_prog` = '$program'";

        $SQL_COND;

        $result = array();
        switch ($action) {
            case 'apps-total':
                $result = $this->fetchAllApplication($SQL_COND);
                break;
            case 'apps-submitted':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(true, $SQL_COND);
                break;

            case 'apps-in-progress':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(false, $SQL_COND);
                break;

            case 'apps-admitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(true, $SQL_COND);
                break;

            case 'apps-unadmitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(false, $SQL_COND);
                break;

            case 'apps-awaiting':
                $result = $this->fetchAllAwaitingApplication($SQL_COND);
                break;
        }
        return $result;
    }

    public function fetchAllApplication($SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 $SQL_COND";
        return $this->dm->getData($query);
    }

    public function fetchAllSubmittedOrUnsubmittecApplication(bool $submitted, $SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.declaration = :s $SQL_COND";
        return $this->dm->getData($query, array(":s" => (int) $submitted));
    }

    public function fetchAllAdmittedOrUnAdmittedApplication(bool $admitted, $SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.admitted = :s $SQL_COND";
        return $this->dm->getData($query, array(":s" => (int) $admitted));
    }

    public function fetchAllAwaitingApplication($SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    form_type AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap, academic_background AS ab 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND ab.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_type = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.declaration = 1 AND ab.awaiting_result = 1$SQL_COND";
        return $this->dm->getData($query);
    }

    public function fetchTotalApplications(int $form_type = 100)
    {
        if ($form_type == 100) {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_type = ft.id";
            return $this->dm->getData($query);
        } else {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_type = ft.id AND ft.id = :f";
            return $this->dm->getData($query, array(":f" => $form_type));
        }
    }

    public function fetchTotalSubmittedOrUnsubmittedApps(int $form_type, bool $submitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                AND pd.form_type = ft.id AND fc.declaration = :s AND ft.id = :f";
        return $this->dm->getData($query, array(":s" => (int) $submitted, ":f" => $form_type));
    }

    public function fetchTotalAdmittedOrUnadmittedApplicants(int $form_type, bool $admitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                pd.form_type = ft.id AND fc.`admitted` = :s AND ft.id = :f";
        return $this->dm->getData($query, array(":s" => (int) $admitted, ":f" => $form_type));
    }

    public function fetchTotalAwaitingResultsByFormType(int $form_type)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_type = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1 AND ft.id = :f";
        return $this->dm->getData($query, array(":f" => $form_type));
    }

    public function fetchTotalAwaitingResults()
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, form_type AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_type = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1";
        return $this->dm->getData($query);
    }

    public function getAllAdmitedApplicants($cert_type)
    {
        $in_query = "";
        if (in_array($cert_type, ["WASSCE", "NECO"])) $in_query = "AND ab.cert_type IN ('WASSCE', 'NECO')";
        if (in_array($cert_type, ["SSSCE", "GBCE"])) $in_query = "AND ab.cert_type IN ('SSSCE', 'GBCE')";
        if (in_array($cert_type, ["BACCALAUREATE"])) $in_query = "AND ab.cert_type IN ('BACCALAUREATE')";

        $query = "SELECT a.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, pg.name AS programme, b.program_choice 
                FROM `personal_information` AS p, `applicants_login` AS a, broadsheets AS b, programs AS pg,  academic_background AS ab  
                WHERE p.app_login = a.id AND b.app_login = a.id AND ab.app_login = a.id AND pg.id = b.program_id AND 
                a.id IN (SELECT b.app_login AS id FROM broadsheets AS b) $in_query";
        return $this->dm->getData($query);
    }

    public function getAllUnadmitedApplicants($certificate, $progCategory)
    {
        $query = "SELECT l.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, 
                    p.`email_addr`, i.`$progCategory` AS programme, a.`cert_type` 
                FROM 
                    `personal_information` AS p, `academic_background` AS a, 
                    `applicants_login` AS l, `form_sections_chek` AS f, `program_info` AS i 
                WHERE 
                    p.`app_login` = l.`id` AND a.`app_login` = l.`id` AND 
                    f.`app_login` = l.`id` AND i.`app_login` = l.`id` AND
                    a.`awaiting_result` = 0 AND f.`declaration` = 1 AND f.`admitted` = 0";
        $param = array();
        if (strtolower($certificate) != "all") {
            $query .= " AND a.`cert_type` = :c";
            $param = array(":c" => $certificate);
        }

        return $this->dm->getData($query, $param);
    }

    public function getAppCourseSubjects(int $loginID)
    {
        $query = "SELECT 
                    r.`type`, r.`subject`, r.`grade` 
                FROM 
                    academic_background AS a, high_school_results AS r, applicants_login AS l
                WHERE 
                    l.`id` = a.`app_login` AND r.`acad_back_id` = a.`id` AND l.`id` = :i";
        return $this->dm->getData($query, array(":i" => $loginID));
    }

    public function getAppProgDetails($program)
    {
        $query = "SELECT `id`, `name` `type`, `group`, `weekend` FROM programs WHERE `name` = :p";
        return $this->dm->getData($query, array(":p" => $program));
    }

    public function bundleApplicantsData($data, $prog_category = "")
    {
        $store = [];
        foreach ($data as  $appData) {
            if ($prog_category == "") $prog_category = $appData["program_choice"];
            $applicant = [];
            $applicant["app_pers"] = $appData;
            $applicant["app_pers"]["prog_category"] = $prog_category;
            $subjs = $this->getAppCourseSubjects($appData["id"]);
            $applicant["sch_rslt"] = $subjs;
            $progs = $this->getAppProgDetails($appData["programme"]);
            $applicant["prog_info"] = $progs;
            array_push($store, $applicant);
        }
        return $store;
    }

    public function fetchAllUnadmittedApplicantsData($certificate, $progCategory)
    {
        $allAppData = $this->getAllUnadmitedApplicants($certificate, $progCategory);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData, $progCategory);
        return $store;
    }

    public function fetchAllAdmittedApplicantsData($cert_type)
    {
        $allAppData = $this->getAllAdmitedApplicants($cert_type);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData);
        return $store;
    }

    public function saveAdmittedApplicantData(int $admin_period, int $appID, int $program_id, $admitted_data, $prog_choice)
    {
        if (empty($appID) || empty($admin_period) || empty($program_id) || empty($admitted_data)) return 0;

        $query = "INSERT INTO `broadsheets` (`admin_period`,`app_login`,`program_id`,
                `required_core_passed`,`any_one_core_passed`,`total_core_score`,`any_three_elective_passed`,
                `total_elective_score`,`total_score`,`program_choice`) 
                VALUES (:ap, :al, :pi, :rcp, :aocp, :tcs, :atep, :tes, :ts, :pc)";
        $params = array(
            ":ap" => $admin_period,
            ":al" => $appID,
            ":pi" => $program_id,
            ":rcp" => $admitted_data["required_core_passed"],
            ":aocp" => $admitted_data["any_one_core_passed"],
            ":tcs" => $admitted_data["total_core_score"],
            ":atep" => $admitted_data["any_three_elective_passed"],
            ":tes" => $admitted_data["total_elective_score"],
            ":ts" => $admitted_data["total_score"],
            ":pc" => $prog_choice
        );
        $this->dm->inputData($query, $params);
    }

    /*
    * Admit applicants in groups by their certificate category
    */
    public function admitApplicantsByCertCat($data, $qualifications)
    {
        $final_result = [];

        foreach ($data as $std_data) {
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["A"])) {
                array_push($final_result, $this->admitByCatA($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["B"])) {
                array_push($final_result, $this->admitByCatB($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["C"])) {
                array_push($final_result, $this->admitByCatC($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["D"])) {
                array_push($final_result, $this->admitByCatD($std_data));
                continue;
            }
        }
        return $final_result;
    }

    public function admitCatAApplicant($app_result, $prog_choice, $cert_type)
    {

        $qualified = false;
        // Admit applicant
        if ($app_result["feed"]["required_core_passed"] == 2 && $app_result["feed"]["any_one_core_passed"] > 0 && $app_result["feed"]["any_three_elective_passed"] >= 3) {

            if (in_array($cert_type, ["SSSCE", "NECO", "GBCE"]) && $app_result["feed"]["total_score"] <= 24) {
                $qualified = true;
            }

            if (in_array($cert_type, ["WASSCE"]) && $app_result["feed"]["total_score"] <= 36) {
                $qualified = true;
            }

            if ($qualified) {
                $query = "UPDATE `form_sections_chek` SET `admitted` = 1, `$prog_choice` = 1 WHERE `app_login` = :i";
                $this->dm->getData($query, array(":i" => $app_result["id"]));
                return $qualified;
            }
        } else {
            $query = "UPDATE `form_sections_chek` SET `admitted` = 0,  `$prog_choice` = 1 WHERE `app_login` = :i";
            $this->dm->getData($query, array(":i" => $app_result["id"]));
            return $qualified;
        }
    }

    public function admitByCatA($data)
    {

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        $total_core_score = 0;
        $required_core_passed = 0;
        $any_one_core_passed = 0;
        $any_one_core_score = 7;

        $any_three_elective_passed = 0;
        $total_elective_score = 0;
        $any_three_elective_scores = [];

        foreach ($data["sch_rslt"] as $result) {

            $score = 7;
            for ($i = 0; $i < count($grade_range); $i++) {
                if ($result["grade"] == $grade_range[$i]["grade"]) {
                    $score = $grade_range[$i]['score'];
                }
            }

            if ($result["type"] == "core") {
                if (strtoupper($result["subject"]) == "CORE MATHEMATICS" || strtoupper($result["subject"]) == "ENGLISH LANGUAGE") {
                    if ($score != 7) {
                        $required_core_passed += 1;
                        $total_core_score += $score;
                    }
                } else {
                    if ($score < $any_one_core_score) {
                        if (!empty($any_one_core_passed)) $total_core_score -= $any_one_core_score;
                        if (empty($any_one_core_passed)) $any_one_core_passed += 1;
                        $total_core_score += $score;
                        $any_one_core_score = $score;
                    }
                }
            }

            if (strtoupper($result["type"]) == "ELECTIVE") {
                if ($score != 7) {
                    $any_three_elective_passed += 1;
                    array_push($any_three_elective_scores, $score);
                }
            }
        }

        $array_before_sort = $any_three_elective_scores;
        asort($any_three_elective_scores);
        $array_with_new_values = array_values($any_three_elective_scores);
        $any_three_elective_scores = array_values($any_three_elective_scores);
        if (count($any_three_elective_scores) > 3) unset($any_three_elective_scores[count($any_three_elective_scores) - 1]);
        $total_elective_score = array_sum($any_three_elective_scores);

        $feed["total_core_score"] = $total_core_score;
        $feed["total_elective_score"] = $total_elective_score;
        $feed["total_score"] = $total_core_score + $total_elective_score;
        $feed["required_core_passed"] = $required_core_passed;
        $feed["any_one_core_passed"] = $any_one_core_passed;
        $feed["any_one_core_score"] = $any_one_core_score;
        $feed["any_three_elective_passed"] = $any_three_elective_passed;
        $feed["any_three_elective_scores"] = $any_three_elective_scores;
        $feed["array_before_sort"] = $array_before_sort;
        $feed["array_with_new_values"] = $array_with_new_values;

        $app_result["id"] = $data["app_pers"]["id"];
        $app_result["feed"] = $feed;
        $app_result["admitted"] = false;
        //$app_result["emailed"] = false;

        $prog_choice = $data["app_pers"]["prog_category"] . "_qualified";

        $app_result["admitted"] = $this->admitCatAApplicant($app_result, $prog_choice, $data["app_pers"]["cert_type"]);
        $admin_period = $this->getCurrentAdmissionPeriodID();

        if ($app_result["admitted"]) {
            $this->saveAdmittedApplicantData($admin_period, $app_result["id"], $data["prog_info"][0]["id"], $app_result["feed"], $data["app_pers"]["prog_category"]);
        }

        $this->logActivity(
            $_SESSION["user"],
            "INSERT",
            "Admitted applicants {$app_result["id"]} through mass admit with following: 
            admission status(addtitted): {$app_result["admitted"]}, admission period = {$admin_period}, 
            program id: {$data["prog_info"][0]["id"]}, program category: {$data["app_pers"]["prog_category"]}"
        );

        return $app_result;
    }

    public function admitByCatB($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitByCatC($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitByCatD($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitQualifiedStudents($certificate, $progCategory)
    {
        $students_bs_data = $this->fetchAllUnadmittedApplicantsData($certificate, $progCategory);

        if (!empty($students_bs_data)) {
            $qualifications = array(
                "A" => array('WASSCE', 'SSSCE', 'GBCE', 'NECO'),
                "B" => array('GCE', "GCE 'A' Level", "GCE 'O' Level"),
                "C" => array('HND'),
                "D" => array('IB', 'International Baccalaureate', 'Baccalaureate'),
            );

            return $this->admitApplicantsByCertCat($students_bs_data, $qualifications);
        }

        return 0;
    }
}