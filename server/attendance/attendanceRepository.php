<?php

namespace attendance;

use database;

class attendanceRepository
{
    private $conn;
    private Database $db;
 public function __construct(Database $db)
 {
    $this->conn = $db->connect();
    $this->db = $db;
 }
public function getMonthlyAttendance($emp_id, $year, $month){

     $sql = "SELECT att_work_date, att_status 
             FROM attendance 
             WHERE emp_id = ? 
               AND YEAR(att_work_date) = ? 
               AND MONTH(att_work_date) = ?
        ORDER BY att_work_date ASC";

     $stmt = $this->conn->prepare($sql);
     $stmt->bind_param("ssi", $emp_id, $year, $month);
     $stmt->execute();

     $result = $stmt->get_result();

    $attendanceList = $result->fetch_all(MYSQLI_ASSOC);
    return !empty($attendanceList) ? $attendanceList : null;
}

    public function insertAttendance($emp_id, $date, $status): bool{

     try {
         return $this->db->insert('attendance', [
             'emp_id' => $emp_id,
             'att_work_date' => $date,
             'att_status' => $status]);
     }catch(\Exception $e){
         $this->conn->rollback();
         return false;
     }
 }

 public function presentDays($emp_id, $year, $month){

         $sql = "SELECT COUNT(*) AS total_present
            FROM attendance
            WHERE emp_id = ?
              AND att_status = 'Present'
              AND YEAR(att_work_date) = ?
              AND MONTH(att_work_date) = ?;";

         $stmt = $this->db->prepare($sql);
         $stmt->bind_param("ssi", $emp_id, $year, $month);
         $result = $stmt->fetch(MYSQLI_ASSOC);

         return (int)($result['total_present'] ?? 0);
     }

    private function getUnpaidDateBoundary($emp_id, $aggregate) {
        $result = $this->db->select(
            'payroll',
            "$aggregate(pay_date) as boundary_date",
            ['emp_id' => $emp_id, 'status' => 'unpaid']
        );

        if (!$result) {
            return null;
        }

        $row = $result->fetch_assoc();
        return $row['boundary_date'] ?? null;
    }

    public function getFirstUnpaidDate($emp_id) {
        return $this->getUnpaidDateBoundary($emp_id, 'MIN');
    }

    public function getLastUnpaidDate($emp_id) {
        return $this->getUnpaidDateBoundary($emp_id, 'MAX');
    }
}