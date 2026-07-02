<?php
/**
 * API Routes Definition
 * Pattern: $router->METHOD('/path/:param', 'ControllerName@methodName');
 * Controller files are in be/controllers/, class name = {Name}Controller
 */

// =====================================================================
// AUTH
// =====================================================================
$router->post('/api/auth/login',      'auth@login');
$router->get ('/api/dashboard/stats', 'auth@stats');

// =====================================================================
// STUDENTS
// =====================================================================
$router->get   ('/api/students',            'student@index');
$router->post  ('/api/students',            'student@store');
$router->get   ('/api/students/create',     'student@create');
$router->get   ('/api/students/export',     'student@export');
$router->post  ('/api/students/import',     'student@processImport');
$router->get   ('/api/students/:id',        'student@view');
$router->get   ('/api/students/:id/edit',   'student@edit');
$router->put   ('/api/students/:id',        'student@update');
$router->delete('/api/students/:id',        'student@delete');
$router->post  ('/api/students/:id/note',   'student@updateNote');

// =====================================================================
// PAYMENTS
// =====================================================================
$router->get   ('/api/payments',                 'payment@index');
$router->post  ('/api/payments',                 'payment@store');
$router->get   ('/api/payments/create',          'payment@create');
$router->get   ('/api/payments/export',          'payment@export');
$router->get   ('/api/payments/refunds',         'payment@refunds');
$router->get   ('/api/payments/my-debts',        'payment@myDebts');
$router->get   ('/api/payments/proofs',          'payment@manageProofs');
$router->post  ('/api/payments/proofs',          'payment@storeProof');
$router->get   ('/api/payments/:id',             'payment@view');
$router->delete('/api/payments/:id',             'payment@delete');
$router->post  ('/api/payments/:id/refund',      'payment@refundStore');
$router->post  ('/api/payments/proofs/:id/approve', 'payment@approveProof');
$router->post  ('/api/payments/proofs/:id/reject',  'payment@rejectProof');
$router->get   ('/api/student-debts/:id',        'payment@getStudentDebts');

// =====================================================================
// FEE TYPES
// =====================================================================
$router->get   ('/api/fee-types',            'feeType@index');
$router->post  ('/api/fee-types',            'feeType@store');
$router->get   ('/api/fee-types/create',     'feeType@create');
$router->post  ('/api/fee-types/import',     'feeType@processImport');
$router->get   ('/api/fee-types/:id',        'feeType@view');
$router->get   ('/api/fee-types/:id/edit',   'feeType@edit');
$router->put   ('/api/fee-types/:id',        'feeType@update');
$router->delete('/api/fee-types/:id',        'feeType@delete');

// =====================================================================
// CLASSES
// =====================================================================
$router->get   ('/api/classes',            'class@index');
$router->post  ('/api/classes',            'class@store');
$router->get   ('/api/classes/create',     'class@create');
$router->get   ('/api/classes/:id/edit',   'class@edit');
$router->put   ('/api/classes/:id',        'class@update');
$router->delete('/api/classes/:id',        'class@delete');

// =====================================================================
// TEACHERS
// =====================================================================
$router->get   ('/api/teachers',            'teacher@index');
$router->post  ('/api/teachers',            'teacher@store');
$router->get   ('/api/teachers/create',     'teacher@create');
$router->get   ('/api/teachers/:id/edit',   'teacher@edit');
$router->put   ('/api/teachers/:id',        'teacher@update');
$router->delete('/api/teachers/:id',        'teacher@delete');

// =====================================================================
// USERS
// =====================================================================
$router->get   ('/api/users',                    'user@index');
$router->post  ('/api/users',                    'user@store');
$router->get   ('/api/users/create',             'user@create');
$router->get   ('/api/profile',                  'user@profile');
$router->put   ('/api/profile',                  'user@updateProfile');
$router->post  ('/api/change-password',          'user@changePassword');
$router->get   ('/api/users/:id/edit',           'user@edit');
$router->put   ('/api/users/:id',                'user@update');
$router->delete('/api/users/:id',                'user@delete');
$router->post  ('/api/users/:id/reset-password', 'user@resetPassword');

// =====================================================================
// REPORTS
// =====================================================================
$router->get('/api/reports',          'report@index');
$router->get('/api/reports/payments', 'report@exportPayments');
$router->get('/api/reports/debts',    'report@exportDebts');

// =====================================================================
// DEBTS (Batch Create)
// =====================================================================
$router->get ('/api/debts/batch', 'debt@createBatch');
$router->post('/api/debts/batch', 'debt@storeBatch');

// =====================================================================
// EXEMPTIONS
// =====================================================================
$router->get   ('/api/exemptions',            'exemption@index');
$router->post  ('/api/exemptions',            'exemption@store');
$router->get   ('/api/exemptions/create',     'exemption@create');
$router->post  ('/api/exemptions/assign',     'exemption@assign');
$router->post  ('/api/exemptions/revoke',     'exemption@revoke');
$router->get   ('/api/exemptions/:id/edit',   'exemption@edit');
$router->put   ('/api/exemptions/:id',        'exemption@update');
$router->delete('/api/exemptions/:id',        'exemption@delete');

// =====================================================================
// ADMIN
// =====================================================================
$router->get ('/api/admin/settings', 'admin@systemSettings');
$router->post('/api/admin/settings', 'admin@updateSettings');
$router->get ('/api/admin/backup',          'admin@backup');
$router->get ('/api/admin/backup/download', 'admin@downloadBackup');

// =====================================================================
// AUDIT LOGS
// =====================================================================
$router->get('/api/audit',        'auditLog@index');
$router->get('/api/audit/export', 'auditLog@export');


