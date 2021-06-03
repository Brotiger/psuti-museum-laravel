<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GraduateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get("/", function(){
    return redirect()->route('login');
});

Route::middleware(['auth:sanctum', 'verified'])->post("/add_employee", [EmployeeController::class, "add_employee"])->name('add_employee');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_employee", [EmployeeController::class, "update_employee"])->name('update_employee');

Route::middleware(['auth:sanctum', 'verified'])->post("/add_unit", [UnitController::class, "add_unit"])->name('add_unit');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_unit", [UnitController::class, "update_unit"])->name('update_unit');

Route::middleware(['auth:sanctum', 'verified'])->get('/employee', [EmployeeController::class, "index"])->name('employee');
Route::middleware(['auth:sanctum', 'verified'])->get('/edit_employee/{id}', [EmployeeController::class, "edit_employee"])->name('edit_employee');
Route::middleware(['auth:sanctum', 'verified'])->get('/employees_list', [EmployeeController::class, "employees_list"])->name('employees_list');

Route::middleware(['auth:sanctum', 'verified'])->get('/unit', [UnitController::class, 'index'])->name('unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/edit_unit/{id}', [UnitController::class, "edit_unit"])->name('edit_unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/units_list', [UnitController::class, "units_list"])->name('units_list');

Route::middleware(['auth:sanctum', 'verified'])->get('/event', [EventController::class, 'index'])->name('event');
Route::middleware(['auth:sanctum', 'verified'])->get('/events_list', [EventController::class, 'events_list'])->name('events_list');
Route::middleware(['auth:sanctum', 'verified'])->post('/add_event', [EventController::class, 'add_event'])->name('add_event');

Route::middleware(['auth:sanctum', 'verified'])->get('/graduate', [GraduateController::class, "index"])->name('graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/graduates_list', [GraduateController::class, "graduates_list"])->name('graduates_list');
Route::middleware(['auth:sanctum', 'verified'])->post("/add_graduate", [GraduateController::class, "add_graduate"])->name('add_graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/more_graduate/{id}', [GraduateController::class, "more_graduate"])->name('more_graduate');