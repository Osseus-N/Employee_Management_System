<?php

namespace employee;

use database\Database;
use PDO;

class employeeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM employees ORDER BY emp_lastname, emp_firstname");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM employees WHERE emp_id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function search(string $term): array
    {
        $like = "%{$term}%";
        $stmt = $this->db->prepare(
            "SELECT * FROM employees
             WHERE emp_firstname LIKE :t1 OR emp_lastname LIKE :t2
                OR emp_position LIKE :t3 OR emp_status LIKE :t4
             ORDER BY emp_lastname, emp_firstname"
        );
        $stmt->execute(['t1' => $like, 't2' => $like, 't3' => $like, 't4' => $like]);

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO employees
                (emp_firstname, emp_lastname, emp_gender, emp_date_of_birth,
                 emp_contact_number, emp_position, emp_hourly_rate, emp_status)
             VALUES
                (:firstname, :lastname, :gender, :dob,
                 :contact, :position, :rate, :status)"
        );
        $stmt->execute([
            'firstname' => $data['emp_firstname'],
            'lastname'  => $data['emp_lastname'],
            'gender'    => $data['emp_gender'],
            'dob'       => $data['emp_date_of_birth'],
            'contact'   => $data['emp_contact_number'] ?? null,
            'position'  => $data['emp_position'],
            'rate'      => $data['emp_hourly_rate'],
            'status'    => $data['emp_status'] ?? 'Active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE employees SET
                emp_firstname = :firstname,
                emp_lastname = :lastname,
                emp_gender = :gender,
                emp_date_of_birth = :dob,
                emp_contact_number = :contact,
                emp_position = :position,
                emp_hourly_rate = :rate,
                emp_status = :status
             WHERE emp_id = :id"
        );

        return $stmt->execute([
            'firstname' => $data['emp_firstname'],
            'lastname'  => $data['emp_lastname'],
            'gender'    => $data['emp_gender'],
            'dob'       => $data['emp_date_of_birth'],
            'contact'   => $data['emp_contact_number'] ?? null,
            'position'  => $data['emp_position'],
            'rate'      => $data['emp_hourly_rate'],
            'status'    => $data['emp_status'] ?? 'Active',
            'id'        => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM employees WHERE emp_id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
