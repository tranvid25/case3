@@ -0,0 +1,23 @@
<?php
return [
    'hr' => [
        'employeeNumber' => 'employeeNumber',  // nếu giữ nguyên tên
        'firstName' => 'firstName',
        'lastName' => 'lastName',
        'payRate' => 'payRate',
        'ssn' => 'ssn',
        'vacationDays' => 'vacationDays',
        'updated_at' => 'updated_at',
        'idEmployee' => 'idEmployee',
    ],
    'pr' => [
        'emp_id' => 'idEmployee',    // ví dụ backend C# có trường khác tên
        'name' => 'firstName',
        'surname' => 'lastName',
        'salary' => 'payRate',
        'social_security_number' => 'ssn',
        'vac_days' => 'vacationDays',
        'last_update' => 'updated_at',
    ]
];
