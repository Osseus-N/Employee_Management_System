<?php

namespace attendance;

use Database;

class attendanceRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->db->connect();
    }

    /**
     * Get all attendance records
     */
    public function getAllAttendance()
    {
        $result = $this->db->select("attendance");

        $attendance = [];

        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }

        return $attendance;
    }

    /**
     * Get attendance by employee
     */
    public function getAttendanceByEmployee($empId)
    {
        $result = $this->db->select(
            "attendance",
            "*",
            [
                "emp_id" => $empId
            ]
        );

        $attendance = [];

        while ($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }

        return $attendance;
    }

    /**
     * Get attendance for one work date
     */
    public function getAttendanceByDate($empId, $date)
    {
        $result = $this->db->select(
            "attendance",
            "*",
            [
                "emp_id" => $empId,
                "att_work_date" => $date
            ]
        );

        return $result->fetch_assoc();
    }

    /**
     * Time In
     */
    public function createAttendance(array $data)
    {
        return $this->db->insert(
            "attendance",
            $data
        );
    }

    /**
     * Time Out
     */
    public function updateAttendance(array $data, array $where)
    {
        return $this->db->update(
            "attendance",
            $data,
            $where
        );
    }

    /**
     * Delete attendance
     */
    public function deleteAttendance($attId)
    {
        return $this->db->delete(
            "attendance",
            [
                "att_id" => $attId
            ]
        );
    }
}