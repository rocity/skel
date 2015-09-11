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

    // $data should be an array
    // $column: the specific column that the user wants to get. (optional)
    // $order: $order['col'] = the column to be ordered
    // $order: $order['mode'] = DESC/ASC
    // TODO: Implement value comparison in queries (>, <, >=, <=)
    // SAMPLE USE
    // $db = new Database();
    // $db->select("users",array("id" => 9, "username" => "kurdapyo",),2,"password");
    public function select($table, $data = array(), $limit = 1, $column = null, $order = array('col' => 'id', 'mode' => 'DESC')) {

        $select = isset($column) ? '`' . $column . '`' : '*';
        $limit = $limit > 1 ? $limit : 1;
        $conditions = array();

        foreach ($data as $dataKey => $dataVal) {
            if (gettype($dataVal) === 'integer') {
                array_push($conditions, ' `'.$dataKey.'`='.$dataVal. ' ');
            } else {
                array_push($conditions, ' `'.$dataKey.'`="'.$dataVal.'" ');
            }
        }

        $condition = '';
        for ($i=0; $i < count($conditions); $i++) { 
            if ($i === 0) {
                $condition .= $conditions[0];
            } else {
                $condition .= ' AND ' . $conditions[$i] . ' ';
            }
        }

        $query = 'SELECT ' . $select . ' FROM `' . $table . '` WHERE ' 
                    . $condition . ' ORDER BY `'. $order['col'] .'` '. $order['mode'] . ' LIMIT ' . $limit .';';

        return $query;
    }
}