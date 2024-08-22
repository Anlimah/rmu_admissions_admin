<?php

namespace Src\System;

use PDOException;
use Src\System\DatabaseConnector;

class DatabaseMethods
{
    private $conn;
    private $stmt;
    private $query;
    private $params;

    function __construct($db, $user, $pass)
    {
        $this->conn = (new DatabaseConnector($db, $user, $pass))->connect();
    }

    private function query($str, $params = array())
    {
        try {
            $this->query = $str;
            $stmt = $this->conn->prepare($str);
            $stmt->execute($params);
            if (explode(' ', $str)[0] == 'SELECT' || explode(' ', $str)[0] == 'CALL') {
                return $stmt->fetchAll();
            } elseif (explode(' ', $str)[0] == 'INSERT' || explode(' ', $str)[0] == 'UPDATE' || explode(' ', $str)[0] == 'DELETE') {
                return 1;
            }
        } catch (PDOException $e) {
            exit(json_encode(array("error" => $e->getMessage())));
        }
    }

    //Get raw data from db
    final public function getID($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result))  return $result[0]["id"];
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //Get raw data from db
    final public function getData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    //Insert, Upadate or Delete Data
    final public function inputData($str, $params = array())
    {
        try {
            $result = $this->query($str, $params);
            if (!empty($result)) return $result;
            return 0;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function run($query, $params = [])
    {
        $this->query = $query;
        $this->params = $params;

        try {
            $this->stmt = $this->conn->prepare($this->query);
            $this->stmt->execute($this->params);
            return $this->stmt;
        } catch (PDOException $e) {
            exit(json_encode(array("error" => $e->getMessage())));
        }
    }

    private function type(): mixed
    {
        return explode(' ', $this->query)[0];
    }

    public function all()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetchAll();
    }

    public function one()
    {
        if ($this->type() == 'SELECT') return $this->stmt->fetch();
    }

    public function add($autoIncrementColumn = null, $primaryKeyValue = null)
    {
        if ($this->type() == 'INSERT') {
            if ($autoIncrementColumn) return $this->conn->lastInsertId($autoIncrementColumn);
            else if ($primaryKeyValue) return $primaryKeyValue;
            else return true;
        }
        return false;
    }

    public function remove()
    {
        if ($this->type() == 'DELETE') return $this->stmt->rowCount();
        return false;
    }

    public function edit()
    {
        if ($this->type() == 'UPDATE') return $this->stmt->rowCount();
        return false;
    }
}
