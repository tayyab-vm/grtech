<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth','isAdmin'])->group(function () {
    
    Route::get('/companies/management', function () { return Inertia::render('Companies/List'); })->name('companies');
    Route::get('/employees/management', function () { return Inertia::render('Employees/List'); })->name('employees');

    Route::resource('companies',CompanyController::class)->except(['create','edit']);
    Route::resource('employees',EmployeeController::class)->except(['create','edit']);;
});

require __DIR__.'/auth.php';
