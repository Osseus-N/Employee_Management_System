<?php

namespace model;

class Employee
{
    public function __construct(
        public ?int $emp_id,
        public string $emp_firstname,
        public string $emp_lastname,
        public string $emp_gender,
        public string $emp_date_of_birth,
        public ?string $emp_contact_number,
        public string $emp_position,
        public float $emp_hourly_rate,
        public string $emp_status = 'Active'
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['emp_id']) ? (int) $data['emp_id'] : null,
            (string) ($data['emp_firstname'] ?? ''),
            (string) ($data['emp_lastname'] ?? ''),
            (string) ($data['emp_gender'] ?? ''),
            (string) ($data['emp_date_of_birth'] ?? ''),
            $data['emp_contact_number'] ?? null,
            (string) ($data['emp_position'] ?? ''),
            (float) ($data['emp_hourly_rate'] ?? 0),
            (string) ($data['emp_status'] ?? 'Active')
        );
    }

    /** @return string[] list of validation error messages (empty = valid) */
    public function validate(): array
    {
        $errors = [];

        if (trim($this->emp_firstname) === '') {
            $errors[] = 'First name is required.';
        }
        if (trim($this->emp_lastname) === '') {
            $errors[] = 'Last name is required.';
        }
        if (!in_array($this->emp_gender, ['Male', 'Female', 'Other'], true)) {
            $errors[] = 'Gender must be Male, Female, or Other.';
        }
        if (trim($this->emp_date_of_birth) === '') {
            $errors[] = 'Date of birth is required.';
        }
        if (trim($this->emp_position) === '') {
            $errors[] = 'Position is required.';
        }
        if ($this->emp_hourly_rate <= 0) {
            $errors[] = 'Hourly rate must be greater than 0.';
        }
        if (!in_array($this->emp_status, ['Active', 'Inactive', 'Terminated'], true)) {
            $errors[] = 'Status must be Active, Inactive, or Terminated.';
        }

        return $errors;
    }
}
