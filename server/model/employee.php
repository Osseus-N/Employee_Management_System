<?php

namespace model;

class Employee
{
    private ?int $empId;
    private string $empFirstname;
    private string $empLastname;
    private string $empGender;
    private ?string $empDateOfBirth;
    private string $empAddress;
    private ?string $empContactNumber;
    private string $empPosition;
    private float $empHourlyRate;
    private string $empStatus;
    private ?string $empCreatedAt;

    public function __construct(
        string $empFirstname,
        string $empLastname,
        string $empGender,
        string $empPosition,
        float $empHourlyRate = 0.00,
        ?string $empDateOfBirth = null,
        ?string $empAddress = null,
        ?string $empContactNumber = null,
        string $empStatus = 'Active',
        ?int $empId = null,
        ?string $empCreatedAt = null
    ) {
        $this->empId = $empId;
        $this->empFirstname = $empFirstname;
        $this->empLastname = $empLastname;
        $this->empGender = $empGender;
        $this->empPosition = $empPosition;
        $this->empHourlyRate = $empHourlyRate;
        $this->empDateOfBirth = $empDateOfBirth;
        $this->empAddress = $empAddress;
        $this->empContactNumber = $empContactNumber;
        $this->empStatus = $empStatus;
        $this->empCreatedAt = $empCreatedAt;
    }

    public function getEmpId(): ?int { return $this->empId; }
    public function getEmpFirstname(): string { return $this->empFirstname; }
    public function getEmpLastname(): string { return $this->empLastname; }
    public function getEmpGender(): string { return $this->empGender; }
    public function getEmpDateOfBirth(): ?string { return $this->empDateOfBirth; }
    public function getEmpContactNumber(): ?string { return $this->empContactNumber; }
    public function getEmpPosition(): string { return $this->empPosition; }
    public function getEmpHourlyRate(): float { return $this->empHourlyRate; }
    public function getEmpStatus(): string { return $this->empStatus; }
    public function getEmpCreatedAt(): ?string { return $this->empCreatedAt; }

    public function getAddress(): ?string { return $this->empAddress; }
    public function toArray(): array
    {
        return [
            'emp_id'             => $this->empId,
            'emp_firstname'      => $this->empFirstname,
            'emp_lastname'       => $this->empLastname,
            'emp_gender'         => $this->empGender,
            'emp_date_of_birth'  => $this->empDateOfBirth,
            'emp_address'        => $this->empAddress,
            'emp_contact_number' => $this->empContactNumber,
            'emp_position'       => $this->empPosition,
            'emp_hourly_rate'    => $this->empHourlyRate,
            'emp_status'         => $this->empStatus,
            'emp_created_at'     => $this->empCreatedAt,
        ];
    }
}