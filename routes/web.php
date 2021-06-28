<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GraduateController;
use App\Http\Middleware\fileCompressionMiddleware;

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
Route::middleware(['auth:sanctum', 'verified'])->get('/employees/'. env('DB_SITE' , 'pguty') .'/more/{id}', [EmployeeController::class, "edit_employee"])->name('edit_employee');
Route::middleware(['auth:sanctum', 'verified'])->get('/employees/'. env('DB_SITE', 'pguty'), [EmployeeController::class, "employees_list"])->name('employees_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_employee', [EmployeeController::class, "search_employee"])->name('search_employee');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_employee', [EmployeeController::class, "delete_employee"])->name('delete_employee');

Route::middleware(['auth:sanctum', 'verified'])->get('/unit', [UnitController::class, 'index'])->name('unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/units/'. env('DB_SITE', 'pguty') .'/more/{id}', [UnitController::class, "edit_unit"])->name('edit_unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/units/'. env('DB_SITE', 'pguty'), [UnitController::class, "units_list"])->name('units_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_unit', [UnitController::class, "search_unit"])->name('search_unit');

Route::middleware(['auth:sanctum', 'verified'])->get('/event', [EventController::class, 'index'])->name('event');
Route::middleware(['auth:sanctum', 'verified'])->get('/events/'. env('DB_SITE', 'pguty'), [EventController::class, 'events_list'])->name('events_list');
Route::middleware(['auth:sanctum', 'verified'])->post('/add_event', [EventController::class, 'add_event'])->name('add_event');
Route::middleware(['auth:sanctum', 'verified'])->get('/events/'. env('DB_SITE', 'pguty') .'/more/{id}', [EventController::class, "edit_event"])->name('edit_event');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_event", [EventController::class, "update_event"])->name('update_event');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_event', [EventController::class, "search_event"])->name('search_event');

Route::middleware(['auth:sanctum', 'verified'])->get('/graduate', [GraduateController::class, "index"])->name('graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/graduates/'. env('DB_SITE', 'pguty'), [GraduateController::class, "graduates_list"])->name('graduates_list');
Route::middleware(['auth:sanctum', 'verified'])->post("/add_graduate", [GraduateController::class, "add_graduate"])->name('add_graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/more_graduate/{id}', [GraduateController::class, "more_graduate"])->name('more_graduate');

Route::middleware(['auth:sanctum', 'verified'])->get('/pages', [PageController::class, "pages_list"])->name('pages_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/pages/more/{id}', [PageController::class, "edit_page"])->name('edit_page');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_page", [PageController::class, "update_page"])->name('update_page');

Route::middleware(['auth:sanctum', 'verified'])->get('/users', [UserController::class, "users_list"])->name('users_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/users/more/{id}', [UserController::class, "edit_user"])->name('edit_user');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_user", [UserController::class, "update_user"])->name('update_user');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_user', [UserController::class, "search_user"])->name('search_user');