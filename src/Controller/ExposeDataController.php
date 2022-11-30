<?php

namespace Src\Controller;

use Twilio\Rest\Client;
use Src\System\DatabaseMethods;

class ExposeDataController extends DatabaseMethods
{

    public function genCode($length = 6)
    {
        $digits = $length;
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    public function validateEmail($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_email = htmlentities(htmlspecialchars($input));
        $sanitized_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) return array("success" => false, "message" => "Invalid email address!");

        return array("success" => true, "message" => $user_email);
    }

    public function validateInput($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9]/', $user_input);

        if ($validated_input) return array("success" => true, "message" => $user_input);

        return array("success" => false, "message" => "Invalid input!");
    }

    public function validateCountryCode($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9()+]/', $user_input);

        if ($validated_input) return array("success" => true, "message" => $user_input);

        return array("success" => false, "message" => "Invalid input!");
    }

    public function validatePassword($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9()+@#.-_=$&!`]/', $user_input);

        if ($validated_input) return array("success" => true, "message" => $user_input);

        return array("success" => false, "message" => "Invalid input!");
    }

    public function validatePhone($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[0-9]/', $user_input);

        if ($validated_input) return array("success" => true, "message" => $user_input);

        return array("success" => false, "message" => "Invalid input!");
    }

    public function validateText($input)
    {
        if (empty($input)) return array("success" => false, "message" => "Input required!");

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z]/', $user_input);

        if ($validated_input) return array("success" => true, "message" => $user_input);

        return array("success" => false, "message" => "Invalid Input!");
    }

    public function validateDate($date)
    {
        if (strtotime($date) === false) return array("success" => false, "message" => "Invalid date!");

        list($year, $month, $day) = explode('-', $date);

        if (checkdate($month, $day, $year)) return array("success" => true, "message" => $date);
    }

    public function validateImage($files)
    {
        if (!isset($files['file']['error']) || !empty($files["pics"]["name"])) {
            $allowedFileType = ['image/jpeg', 'image/png', 'image/jpg'];
            for ($i = 0; $i < count($files["pics"]["name"]); $i++) {
                $check = getimagesize($files["pics"]["tmp_name"][$i]);
                if ($check !== false && in_array($files["pics"]["type"][$i], $allowedFileType)) {
                    return array("success" => true, "message" => $files);
                }
            }
        }
        return array("success" => false, "message" => "Invalid file uploaded!");
    }

    public function validateInputTextOnly($input)
    {
        if (empty($input)) {
            return array("success" => false, "message" => "required");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z]/', $user_input);

        if ($validated_input) {
            return array("success" => true, "message" => $user_input);
        }

        return array("success" => false, "message" => "invalid");
    }

    public function validateInputTextNumber($input)
    {
        if (empty($input)) {
            return array("success" => false, "message" => "required");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[A-Za-z0-9]/', $user_input);

        if ($validated_input) {
            return array("success" => true, "message" => $user_input);
        }

        return array("success" => false, "message" => "invalid");
    }

    public function validateYearData($input)
    {
        if (empty($input) || strtoupper($input) == "YEAR") {
            return array("success" => false, "message" => "required");
        }

        if ($input < 1990 || $input > 2022) {
            return array("success" => false, "message" => "invalid");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        $validated_input = (bool) preg_match('/^[0-9]/', $user_input);

        if ($validated_input) {
            return array("success" => true, "message" => $user_input);
        }

        return array("success" => false, "message" => "invalid");
    }

    public function validateGrade($input)
    {
        if (empty($input) || strtoupper($input) == "GRADE") {
            return array("success" => false, "message" => "required");
        }

        if (strlen($input) < 1 || strlen($input) > 2) {
            return array("success" => false, "message" => "invalid");
        }

        $user_input = htmlentities(htmlspecialchars($input));
        return array("success" => true, "message" => $user_input);
    }

    public function getIPAddress()
    {
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getDeciveInfo()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function getFormPrice(string $form_type)
    {
        return $this->getData("SELECT `amount` FROM `form_type` WHERE `name` LIKE '%$form_type%'");
    }

    public function getAdminYearCode()
    {
        $sql = "SELECT EXTRACT(YEAR FROM (SELECT `start_date` FROM admission_period)) AS 'year'";
        $year = (string) $this->getData($sql)[0]['year'];
        return (int) substr($year, 2, 2);
    }

    public function getFormTypes()
    {
        return $this->getData("SELECT * FROM `form_type`");
    }

    public function getPaymentMethods()
    {
        return $this->getData("SELECT * FROM `payment_method`");
    }

    public function getPrograms($type)
    {
        $sql = "SELECT * FROM `programs` WHERE `type` = :t";
        $param = array(":t" => $type);
        return $this->getData($sql, $param);
    }

    public function getHalls()
    {
        return $this->getData("SELECT * FROM `halls`");
    }

    public function sendEmail($recipient_email, $subject, $message)
    {
        $headers = 'MIME-Version: 1.0';
        $headers .= 'Content-Type: text/html; charset=UTF-8';
        $headers .= 'From: admissions@rmuictonline.com';
        $headers .= 'To: ' . $recipient_email;
        $headers .= 'Subject: ' . $subject;

        $success = mail($recipient_email, $subject, $message, $headers);
        if ($success) return 1;
        return 0;
    }

    public function sendSMS($recipient_number, $otp_code, $message, $ISD)
    {
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_TKN');
        $client = new Client($sid, $token);

        //prepare SMS message
        $to = $ISD . $recipient_number;
        $account_phone = getenv('TWILIO_PNM');
        $from = array('from' => $account_phone, 'body' => $message . ' ' . $otp_code);

        //send SMS
        $response = $client->messages->create($to, $from);
        if ($response->sid) {
            //$_SESSION['sms_sid'] = $response->sid;
            return $otp_code;
        } else {
            return 0;
        }
    }

    public function sendOTP($phone_number, $country_code)
    {
        $otp_code = $this->genCode(4);
        $message = 'Your OTP verification code is';
        return $this->sendSMS($phone_number, $otp_code, $message, $country_code);
    }

    public function getVendorPhone($vendor_id)
    {
        $sql = "SELECT `country_code`, `phone_number` FROM `vendor_details` WHERE `id`=:i";
        return $this->getData($sql, array(':i' => $vendor_id));
    }

    public function vendorExist($vendor_id)
    {
        $str = "SELECT `id` FROM `vendor_details` WHERE `id`=:i";
        return $this->getID($str, array(':i' => $vendor_id));
    }

    public function verifyVendorLogin($username, $password)
    {
        $sql = "SELECT `vendor`, `password` FROM `vendor_login` WHERE `user_name` = :u";
        $data = $this->getData($sql, array(':u' => sha1($username)));
        if (!empty($data)) {
            if (password_verify($password, $data[0]["password"])) {
                return array("success" => true, "message" => $data[0]["vendor"]);
            } else {
                return array("success" => false, "message" => "No match found!");
            }
        }
        return array("success" => false, "message" => "User does not exist!");
    }

    public function getApplicationInfo(int $transaction_id)
    {
        $sql = "SELECT p.`app_number`, p.`pin_number`, f.`name`, f.`amount`, v.`vendor_name`, a.`info`, f.`name`  
        FROM `purchase_detail` AS p, `form_type` AS f, `vendor_details` AS v, `admission_period` AS a 
        WHERE p.`form_type` = f.`id` AND p.vendor = v.`id` AND p.`admission_period` = a.`id` AND p.`id` = :i";
        return $this->getData($sql, array(':i' => $transaction_id));
    }
}
