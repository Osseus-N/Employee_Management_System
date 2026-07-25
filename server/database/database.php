<?php

class database
{
    private mysqli $conn;

    public function connect(){

        try{
            $dsn ="mysql:host=localhost;dbname=user_db";
            $this->conn = new mysqli( $dsn, "root", "", "employee_management" );
        }catch(mysqli_sql_exception $e){
            die ("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
    public function disconnect(){
        $this->conn->close();
    }
    public function insert($table,$data)
    {
        try{
            $table_columns = implode(",", array_keys($data));
            $prep = $types = "";

            foreach ($data as $key => $value) {
                $prep .= "?,";
                $types .= substr(gettype($value), 0, 1);
            }
            $prep = substr($prep, 0, -1);
            $stmt = $this->conn->prepare("INSERT INTO $table ($table_columns) VALUES ($prep)");
            $stmt->bind_param($types, ...array_values($data));
            $stmt->execute();
            $stmt->close();
        }catch(Exception $e){
            die('Insert Error: ' . $e->getMessage());
        }
    }
    public function select($table, $row="*",$where=NULL){
        try{
            if(!is_null($where)){
                $cond=$types="";

                foreach ($where as $key => $value) {
                    $cond.=$key."=? AND ";
                    $types .= substr(gettype($value), 0, 1);
                }
                $cond = substr($cond, 0, -4);
                $stmt = $this->conn->prepare("SELECT $row FROM $table WHERE $cond");
                $stmt->bind_param($types, ...array_values($where));
            }else {
                $stmt = $this->conn->prepare("SELECT $row FROM $table");
            }
            $stmt->execute();
            $this->res = $stmt->get_result();
        }catch(Exception $e){
            die('Select Error: ' . $e->getMessage());
        }
    }
}