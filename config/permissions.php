<?php

return [

    // Admin memiliki seluruh hak akses
    'admin' => ['*'],

    'doctor' => [
        'appointments.read',
        'appointments.create',
        'appointments.update',
        'appointments.delete',
        'patients.read',
        'treatments.create',
        'treatments.update',
        'bed_admissions.create',
        'bed_admissions.update',
        'combined_treatments.read',
        'staff_schedules.read',
        'staff.read',
        'staff.update',
    ],

    'nurse' => [
        'patients.read',
        'doctors.read',
        'appointments.read',
        'treatments.read',
        'treatment_categories.read',
        'bed_admissions.read',
        'bed_admissions.create',
        'bed_admissions.update',
        'hospital_beds.read',
        'hospital_beds.update',
        'staff.read',
        'staff.update',
        'staff_schedules.read',
        'staff_schedules.update',
        'combined_treatments.read',
    ],

    'receptionist' => [
        'patients.read',
        'patients.create',
        'patients.update',
        'appointments.read',
        'appointments.create',
        'appointments.update',
        'billing.read',
        'billing.create',
        'billing.update',
        'staff_schedules.read',
        'staff.read',
        'staff.update',
    ],
];

//     // Doctor hanya boleh mengelola appointment
//     'doctor' => [
//         'appointments.read',
//         'appointments.create',
//         'appointments.update',
//         'appointments.delete',
//         'treatments.create',
//         'treatments.update',
//         'bed_admissions.create',
//         'bed_admissions.update',
//         'combined_treatments.read',
//         'staff_schedules.read',
//         'staff.read',
//         'staff.update',
//     ],

//     // Nurse
//     'nurse' => [
//         'patients.read',
//         'doctors.read',
//         'appointments.read',
//         'treatments.read',
//         'treatment_categories.read',
//         'bed_admissions.read',
//         'bed_admissions.create',
//         'bed_admissions.update',
//         'hospital_beds.read',
//         'hospital_beds.update',
//         'staff.read',
//         'staff.update',
//         'staff_schedules.read',
//         'staff_schedules.update',
//         'combined_treatments.read',
//     ],

//     // Receptionist
//     'receptionist' => [
//         'patients.read',
//         'patients.create',
//         'patients.update',
//         'appointments.read',
//         'appointments.create',
//         'appointments.update',
//         'billing.read',
//         'billing.create',
//         'billing.update',
//         'staff_schedules.read',
//         'staff.read',
//         'staff.update',
//     ],

// ];

// //
// 'doctor' => [
//     'appointments:create',
//     'appointments:update',
//     'treatments:create',
//     'treatments:update',
//     'bed_admissions:create',
//     'bed_admissions:update',
//     'combined_treatments:read',
//     'staff_schedules:read',
//     'staff:read',
//     'staff:update',
//
// ],
// 'nurse' => [
//     'patients:update',
//     'bed_admissions:create',
//     'bed_admissions:update',
//     'bed_admissions:delete',
//     'hospital_beds:update'
//     'staff_schedules:read',
//     'staff:read',
//     'staff:update',
//     'combined_treatments:read',
// ],
// 'receptionist' => [
//     'patients:create',
//     'patients:update',
//     'appointments:create',
//     'appointments:update',
//     'billing:create'
//     'staff_schedules:read',
//     'staff:read',
//     'staff:update',
// ]