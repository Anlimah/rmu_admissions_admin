<?php

namespace Src\Base;

use Src\Controller\ExposeDataController;
use Src\System\DatabaseMethods;

class Program
{

    private $dm = null;
    private $expose = null;
    private $log = null;

    public function __construct($db, $user, $pass)
    {
        $this->dm = new DatabaseMethods($db, $user, $pass);
        $this->expose = new ExposeDataController($db, $user, $pass);
        $this->log = new Log($db, $user, $pass);
    }

    public function fetchAllPrograms()
    {
        $query = "SELECT p.`id`, p.`name`, f.name AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, forms AS f WHERE p.type = f.id";
        return $this->dm->getData($query);
    }

    public function fetchProgramme($prog_id)
    {
        $query = "SELECT p.`id`, p.`name`, f.id AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, forms AS f WHERE p.type = f.id AND p.id = :i";
        return $this->dm->getData($query, array(":i" => $prog_id));
    }

    public function fetchAllFromProgramByName($prog_name)
    {
        return $this->dm->getData("SELECT * FROM programs WHERE `name` = :n", array(":n" => $prog_name));
    }

    public function fetchAllFromProgramByID($prog_id)
    {
        return $this->dm->getData("SELECT * FROM programs WHERE `id` = :i", array(":i" => $prog_id));
    }

    public function fetchAllFromProgramByCode($prog_code)
    {
        return $this->dm->getData("SELECT * FROM programs WHERE `program_code` = :c", array(":c" => $prog_code));
    }

    public function addProgramme($prog_name, $prog_type, $prog_wkd, $prog_grp)
    {
        $query = "INSERT INTO programs (`name`, `type`, `weekend`, `group`) VALUES(:n, :t, :w, :g)";
        $params = array(":n" => strtoupper($prog_name), ":t" => $prog_type, ":w" => $prog_wkd, ":g" => $prog_grp);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->log->activity(
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
            $this->log->activity(
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
            $this->log->activity(
                $_SESSION["user"],
                "DELETE",
                "Deleted programme {$prog_id}"
            );
        return $query_result;
    }
}
