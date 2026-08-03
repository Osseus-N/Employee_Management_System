<?php

namespace attendance;

use Exception;
use response\responseController;
use service\SessionManager;

class attendanceController extends responseController
{
    private attendanceService $attendanceService;

    public function __construct(attendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function handleRequest(): void
    {
        SessionManager::init();

        if (!SessionManager::isLoggedIn()) {
            $this->error('Please log in first.', 401);
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getAttendance();
                break;
            case 'POST':
                $this->clockIn();
                break;
            case 'PUT':
                $this->clockOut();
                break;
            default:
                $this->error('Method not allowed', 405);
        }
    }

    public function getAttendance(): void
    {
        if (SessionManager::isAdmin() && empty($_GET['emp_id'])) {
            $this->success('Attendance retrieved successfully', $this->attendanceService->getAll());
        }

        $empId = (!empty($_GET['emp_id']) && SessionManager::isAdmin())
            ? (int) $_GET['emp_id']
            : SessionManager::currentEmpId();

        $this->success('Attendance retrieved successfully', $this->attendanceService->getByEmployee($empId));
    }

    public function clockIn(): void
    {
        try {
            $result = $this->attendanceService->clockIn(SessionManager::currentEmpId());
            $this->success('Clocked in successfully', $result, 201);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }

    public function clockOut(): void
    {
        try {
            $result = $this->attendanceService->clockOut(SessionManager::currentEmpId());
            $this->success('Clocked out successfully', $result);
        } catch (Exception $e) {
            $this->error($e->getMessage(), 400);
        }
    }
}
