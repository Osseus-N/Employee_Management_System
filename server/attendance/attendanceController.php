<?php

namespace attendance;

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
        SessionManager::isLoggedIn();

        switch ($_SERVER["REQUEST_METHOD"]) {

            case "GET":
                $this->handleGet();
                break;

            case "POST":
                $this->handlePost();
                break;

            case "DELETE":
                $this->deleteAttendance();
                break;

            default:
                $this->error("Method Not Allowed", 405);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET
    |--------------------------------------------------------------------------
    */

    private function handleGet(): void
    {
        // Admin: View all attendance
        if (
            isset($_SESSION["role"]) &&
            $_SESSION["role"] === "admin"
        ) {

            $attendance = $this->attendanceService
                ->getAllAttendance();

            $this->success(
                "Attendance retrieved successfully",
                $attendance
            );
        }

        // Employee: View own attendance
        $attendance = $this->attendanceService
            ->getAttendanceByEmployee(
                $_SESSION["emp_id"]
            );

        $this->success(
            "Attendance retrieved successfully",
            $attendance
        );
    }

    /*
    |--------------------------------------------------------------------------
    | POST
    |--------------------------------------------------------------------------
    */

    private function handlePost(): void
    {
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!isset($data["action"])) {
            $this->error("Action is required");
        }

        switch ($data["action"]) {

            case "time_in":

                if (
                    $this->attendanceService->timeIn(
                        $_SESSION["emp_id"]
                    )
                ) {

                    $this->success(
                        "Time In recorded successfully"
                    );

                } else {

                    $this->error(
                        "You already timed in today."
                    );

                }

                break;

            case "time_out":

                if (
                    $this->attendanceService->timeOut(
                        $_SESSION["emp_id"]
                    )
                ) {

                    $this->success(
                        "Time Out recorded successfully"
                    );

                } else {

                    $this->error(
                        "Unable to record Time Out."
                    );

                }

                break;

            default:

                $this->error(
                    "Invalid action"
                );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    private function deleteAttendance(): void
    {
        if (
            !isset($_SESSION["role"]) ||
            $_SESSION["role"] !== "admin"
        ) {

            $this->error(
                "Unauthorized",
                403
            );

        }

        $data = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!isset($data["att_id"])) {

            $this->error(
                "Attendance ID is required",
                400
            );

        }

        $deleted = $this->attendanceService
            ->deleteAttendance(
                (int)$data["att_id"]
            );

        if (!$deleted) {

            $this->error(
                "Unable to delete attendance"
            );

        }

        $this->success(
            "Attendance deleted successfully"
        );
    }
}