<?php

class database
{
    private mysqli $conn;

    public function connect(){

        try{
            $dsn ="mysql:host=localhost;dbname=user_db";
            $this->conn = new mysqli( $dsn, "root", "", "user_db" );
        }catch(mysqli_sql_exception $e){
            die ("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
    public function disconnect(){
        $this->conn->close();
    }
}