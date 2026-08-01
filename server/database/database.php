<?php

class Database
{
    private mysqli $conn;

    /**
     * Automatically connect when Database is created.
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Connect to MySQL Database
     */
    public function connect(): mysqli
    {
        if (isset($this->conn)) {
            return $this->conn;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {

            $this->conn = new mysqli(
                "localhost",
                "root",
                "",
                "employee_management"
            );

            $this->conn->set_charset("utf8mb4");

        } catch (mysqli_sql_exception $e) {

            throw new Exception(
                "Database Connection Failed: " .
                $e->getMessage()
            );

        }

        return $this->conn;
    }

    /**
     * Return active MySQL connection
     */
    public function getConnection(): mysqli
    {
        return $this->conn;
    }

    /**
     * Close database connection
     */
    public function disconnect(): void
    {
        if (isset($this->conn)) {

            $this->conn->close();

            unset($this->conn);

        }
    }

    /**
     * Determine bind_param() type
     */
    private function getTypeChar($value): string
    {
        if (is_int($value)) {
            return "i";
        }

        if (is_float($value)) {
            return "d";
        }

        return "s";
    }

    /**
     * Build WHERE clause
     *
     * Example:
     * ["emp_id"=>5,"status"=>"Active"]
     *
     * becomes
     *
     * emp_id=? AND status=?
     */
    private function buildWhere(array $where): array
    {
        $conditions = [];
        $types = "";
        $values = [];

        foreach ($where as $column => $value) {

            $conditions[] = "$column = ?";

            $types .= $this->getTypeChar($value);

            $values[] = $value;

        }

        return [

            implode(" AND ", $conditions),

            $types,

            $values

        ];
    }
}

    /**
     * Insert Data
     */
    public function insert(string $table, array $data)
    {
        try {

            $columns = implode(", ", array_keys($data));

            $placeholders = implode(", ", array_fill(0, count($data), "?"));

            $types = "";
            $values = [];

            foreach ($data as $value) {

                $types .= $this->getTypeChar($value);
                $values[] = $value;

            }

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param($types, ...$values);

            $stmt->execute();

            return $this->conn->insert_id;

        }

        catch (Exception $e) {

            throw new Exception(
                "Insert Error: " .
                $e->getMessage()
            );

        }

    }

    /**
     * Select Data
     */
    public function select(
        string $table,
        string $columns = "*",
        array $where = []
    ) {

        try {

            $sql = "SELECT {$columns} FROM {$table}";

            if (!empty($where)) {

                list($condition, $types, $values) =
                    $this->buildWhere($where);

                $sql .= " WHERE " . $condition;

            }

            $stmt = $this->conn->prepare($sql);

            if (!empty($where)) {

                $stmt->bind_param($types, ...$values);

            }

            $stmt->execute();

            return $stmt->get_result();

        }

        catch (Exception $e) {

            throw new Exception(
                "Select Error: " .
                $e->getMessage()
            );

        }

    }

        /**
         * Update Data
         */
        public function update(string $table, array $data, array $where): bool
        {
            try {

                if (empty($data) || empty($where)) {
                    return false;
                }

                $set = [];
                $types = "";
                $values = [];

                foreach ($data as $column => $value) {

                    $set[] = "$column = ?";
                    $types .= $this->getTypeChar($value);
                    $values[] = $value;

                }

                list($condition, $whereTypes, $whereValues) =
                    $this->buildWhere($where);

                $types .= $whereTypes;
                $values = array_merge($values, $whereValues);

                $sql = "UPDATE {$table}
                        SET " . implode(", ", $set) . "
                        WHERE {$condition}";

                $stmt = $this->conn->prepare($sql);

                $stmt->bind_param($types, ...$values);

                return $stmt->execute();

            }

            catch (Exception $e) {

                throw new Exception(
                    "Update Error: " .
                    $e->getMessage()
                );

            }
        }

        /**
         * Delete Data
         */
        public function delete(string $table, array $where): bool
        {
            try {

                list($condition, $types, $values) =
                    $this->buildWhere($where);

                $sql = "DELETE FROM {$table}
                        WHERE {$condition}";

                $stmt = $this->conn->prepare($sql);

                $stmt->bind_param($types, ...$values);

                return $stmt->execute();

            }

            catch (Exception $e) {

                throw new Exception(
                    "Delete Error: " .
                    $e->getMessage()
                );

            }
        }

        /**
         * Begin Transaction
         */
        public function beginTransaction(): void
        {
            $this->conn->begin_transaction();
        }

        /**
         * Commit Transaction
         */
        public function commit(): void
        {
            $this->conn->commit();
        }

        /**
         * Rollback Transaction
         */
        public function rollback(): void
        {
            $this->conn->rollback();
        }