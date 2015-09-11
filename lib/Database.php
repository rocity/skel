<?php
require_once 'Config.php';
/**
* Database functions and configurations
*/
class Database extends Config
{
    protected $conn;

    function __construct()
    {
        // connect to db when this class is created
        $dbc = new mysqli(
            Config::$dbhost,
            Config::$dbuser,
            Config::$dbpass,
            Config::$dbname,
            Config::$dbport,
            Config::$dbsock
            );
        if ($dbc->connect_error) {
            die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        } else {
            $this->conn = $dbc;
        }
    }

    public function selectUsers() {
        $con = $this->conn;
        $q = 'SELECT * FROM `users` LIMIT 10';
        $result = $con->query($q);
        if ($result->num_rows > 0) {
            $ret = array();
            while ($row = $result->fetch_assoc()) {
                array_push($ret, $row);
            }
            $ret['query_count'] = $result->num_rows;
            return $ret;
        }
        return false;
    }
}