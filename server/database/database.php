<?php

class Database
{
    private mysqli $conn;

    // Connect to Database
    public function connect()
    {
        try {

            $this->conn = new mysqli(
                "localhost",
                "root",
                "",
                "employee_management"
            );

            if ($this->conn->connect_error) {
                die("Connection Failed: " . $this->conn->connect_error);
            }

        } catch (mysqli_sql_exception $e) {

            die("Database Error: " . $e->getMessage());

        }

        return $this->conn;
    }

    // Close Connection
    public function disconnect()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // Insert Data
    public function insert($table, $data)
    {
        try {

            $columns = implode(",", array_keys($data));

            $values = "";
            $types = "";

            foreach ($data as $value) {

                $values .= "?,";
                $types .= substr(gettype($value), 0, 1);

            }

            $values = rtrim($values, ",");

            $sql = "INSERT INTO $table ($columns) VALUES ($values)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param($types, ...array_values($data));

            $stmt->execute();

            return true;

        } catch (Exception $e) {

            die("Insert Error: " . $e->getMessage());

        }
    }

    // Select Data
    public function select($table, $row = "*", $where = null)
    {
        try {
            if (!empty($where) && is_array($where)) {
                $conditions = [];
                $types = "";

                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                    $types .= substr(gettype($value), 0, 1);
                }

                // Cleanly join conditions with " AND "
                $conditionString = implode(' AND ', $conditions);

                $sql = "SELECT $row FROM $table WHERE $conditionString";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param($types, ...array_values($where));

            } else {
                $sql = "SELECT $row FROM $table";
                $stmt = $this->conn->prepare($sql);
            }

            $stmt->execute();
            return $stmt->get_result();

        } catch (Exception $e) {
            die("Select Error: " . $e->getMessage());
        }
    }

    // Update Data
    public function update($table, $data, $where)
    {
        try {

            $set = "";
            $condition = "";
            $types = "";

            foreach ($data as $key => $value) {

                $set .= "$key=?,";
                $types .= substr(gettype($value), 0, 1);

            }

            $set = rtrim($set, ",");

            foreach ($where as $key => $value) {

                $condition .= "$key=? AND ";
                $types .= substr(gettype($value), 0, 1);

            }

            $condition = substr($condition, 0, -5);

            $sql = "UPDATE $table SET $set WHERE $condition";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param(
                $types,
                ...array_merge(array_values($data), array_values($where))
            );

            return $stmt->execute();

        } catch (Exception $e) {

            die("Update Error: " . $e->getMessage());

        }
    }

    // Delete Data
    public function delete($table, $where)
    {
        try {

            $condition = "";
            $types = "";

            foreach ($where as $key => $value) {

                $condition .= "$key=? AND ";
                $types .= substr(gettype($value), 0, 1);

            }

            $condition = substr($condition, 0, -5);

            $sql = "DELETE FROM $table WHERE $condition";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param($types, ...array_values($where));

            return $stmt->execute();

        } catch (Exception $e) {

            die("Delete Error: " . $e->getMessage());

        }
    }
}