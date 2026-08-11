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
    public function getMonthlyAttendance($emp_id, $month, $year): ?array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate   = date('Y-m-d', strtotime("$startDate +1 month"));

        $sql = "SELECT att_work_date, att_status 
            FROM attendance 
            WHERE emp_id = ? 
              AND att_work_date >= ? 
              AND att_work_date < ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $emp_id, $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insertAttendance($emp_id, $date): bool{

     try {
         return $this->db->insert('attendance', [
             'emp_id' => $emp_id,
             'att_work_date' => $date,
             'att_status' => 'UnPaid']);
     }catch(\Exception $e){
         $this->conn->rollback();
         return false;
     }
 }
    public function presentDaysBetween($emp_id, $payroll_start_date, $payroll_end_date): int {

        $sql = "SELECT COUNT(*) AS total_present
                FROM attendance
                WHERE emp_id = ?
                    AND att_status = 'Unpaid'
                AND att_work_date BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($sql);

        if(!$stmt){
            return false;
        }

        $stmt->bind_param("iss",$emp_id,$payroll_start_date,$payroll_end_date);
        $stmt->execute();

        $result = $stmt->get_result();

        if (!$result) {
            return false;
        }
        $row = $result->fetch_assoc();

        return (int)($row['total_present'] ?? 0);
    }
    public function checkAttendance($emp_id, $date): bool
    {

        $result = $this->db->select('attendance', '1', [
            'emp_id'        => $emp_id,
            'att_work_date' => $date
        ]);

        return $result->num_rows > 0;
    }
}