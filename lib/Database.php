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

    // $data should be an array
    // $column: the specific column that the user wants to get. (optional)
    // $order: $order['col'] = the column to be ordered
    // $order: $order['mode'] = DESC/ASC
    // TODO: Implement value comparison in queries (>, <, >=, <=)
    // SAMPLE USE
    // $db = new Database();
    // $db->select("users",array("id" => 9, "username" => "kurdapyo",),2,"password");
    public function select($table, $data = array(), $limit = 1, $column = null, $order = array('col' => 'id', 'mode' => 'DESC')) {
        $conn = $this->conn;
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

        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $ret = array();
            while ($row = $result->fetch_assoc()) {
                array_push($ret, $row);
            }
            return $ret;
        }
        return false;
    }

    /*
    * Insert data into the database
    * Example usage:
    * $db = new Database();
    * $date = date('Y-m-d h:i:s');
    * $db->insert("users", array('username' => 'user2', 'password' => 'qwery', 'type' => 1, 'status' => 0, 'modified' => $date, 'created' => $date));
    */
    public function insert($table, $data = array()) {
        $conn = $this->conn;
        if (!$table || $table == '') {
            return false;
        }

        $fields = '';
        $values = '';
        foreach ($data as $dataKey => $dataVal) {
            $fields .= '`'. $dataKey .'`, ';

            if (gettype($dataVal) === 'integer') {
                $values .= $dataVal . ', ';
            } else {
                $values .= '"'. $dataVal . '", ';
            }
        }

        $fields = rtrim($fields, ', ');
        $values = rtrim($values, ', ');

        $query = 'INSERT INTO `'. $table . '` (' . $fields . ') VALUES (' . $values . ');';

        if ($result = $conn->query($query)) {
            return $conn->insert_id;
        }
        return false;
    }
}