<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataHandler
 *
 * @author kiran
 */
class DataHandler {

    //put your code here
    var $db_link;

    public function __construct() {
        
    }

    private function getDbReplicaHost(){
    	return REPLICA_HOST;
    } 
    
    
    private function getUserName() {
        return DB_USERNAME;
    }

    private function getPassword() {
        return DB_PASS;
    }

    private function getDatabase() {
        return DB;
    }

    public function connect() {
        $link = mysql_connect('localhost', DataHandler::getUserName(), DataHandler::getPassword());
        //$link = mysql_connect('46.137.222.170', DataHandler::getUserName(), DataHandler::getPassword());
        if (!$link) {
            die('Could not connect :' . mysql_error());
        } else {
            $this->db_link = $link;
            mysql_select_db(DataHandler::getDatabase());
        }
    }

    public function Disconnect() {
        mysql_close($this->db_link);
    }

    public function LastInsertId() {
        return mysql_insert_id();
    }

    public function GetQuery($sql) {

        try {
            $this->connect();
            $result_set = "";
            $cnt = 0;
            $result = mysql_query($sql);

            $error = mysql_errno($this->db_link);


            if ($error != 0) {

                if ($_GET['debug']) {
                	echo "<pre>";
                	print_r($sql);
                	echo "</pre>";
                    echo "<pre>";
                    print_r(mysql_error());
                    echo "</pre>";
                }
//                echo "<pre>";
//                print_r($sql);
//                echo "</pre>";
                return -1;
            }


            if (mysql_affected_rows($this->db_link)) {
                //echo $sql;
                while ($row = mysql_fetch_assoc($result)) {
                    $result_set[$cnt] = $row;
                    $cnt++;
                }
            } else {
                $result_set = -1;
            }
            $this->Disconnect($this->db_link);
            return $result_set;
        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            echo "</pre>";
        }
    }

    public function PutQuery($sql) {
        $this->connect($this->db_link);
        $ret = '';
        try {

            $res = mysql_query($sql);
            $error = mysql_error();

            $ret['result'] = $res;
            $ret['error'] = $error;

            $this->Disconnect($this->db_link);
        } catch (Exception $e) {
            
        }
        return $ret;
    }

    public function NoConnectPutQuery($sql) {
        $ret = '';
        try {
            $res = mysql_query($sql, $this->db_link);
            $error = mysql_error();

            $ret['result'] = $res;
            $ret['error'] = $error;
        } catch (Exception $e) {
            
        }
        return $ret;
    }

    public function NoConnectGetQuery($sql) {
        $res = '';
        $result_set =  array();
        try {
            $res = mysql_query($sql, $this->db_link);


            if (mysql_affected_rows($this->db_link)) {
                //echo $sql;
                $cnt = 0;
                while ($row = mysql_fetch_assoc($res)) {
                    $result_set[$cnt] = $row;
                    $cnt++;
                }
            } else {
                $result_set = -1;
            }

        } catch (Exception $e) {
            
        }
        return $result_set;
    }

    public function Proc($proc) {
        $res = mysql_query($proc);
        $error = mysql_errno();
        if ($error == 0)
            return $res;
        else
            return -1;
    }

    public function GetProcOutput($sql) {
        $res = mysql_query($sql);
        $error = mysql_errno();
        $cnt = 0;
        //echo $sql;
        if ($error == 0) {
            $result_set = "";
            //echo "printing row";
            while ($row = mysql_fetch_assoc($res)) {
                $result_set[$cnt] = $row;
                $cnt++;
            }
            return $result_set;
        } else {
            return -1;
        }
    }
    public function getErrorNo(){
        return mysql_errno($this->db_link);
    }
    public function getErrorText(){
        return mysql_error();
    }

}

?>
