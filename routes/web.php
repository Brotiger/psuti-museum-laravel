<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HeroController;
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
Route::middleware(['auth:sanctum', 'verified'])->get('/employees/{site}/more/{id}', [EmployeeController::class, "edit_employee"])->where('site', 'pguty|psuti|ks')->name('edit_employee');
Route::middleware(['auth:sanctum', 'verified'])->get('/employees/{site}', [EmployeeController::class, "employees_list"])->where('site', 'pguty|psuti|ks')->name('employees_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_employee', [EmployeeController::class, "search_employee"])->name('search_employee');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_employee', [EmployeeController::class, "delete_employee"])->name('delete_employee');

Route::middleware(['auth:sanctum', 'verified'])->get('/hero', [HeroController::class, "index"])->name('hero');
Route::middleware(['auth:sanctum', 'verified'])->post("/add_hero", [HeroController::class, "add_hero"])->name('add_hero');
Route::middleware(['auth:sanctum', 'verified'])->get('/heroes/{site}', [HeroController::class, "heroes_list"])->where('site', 'pguty|psuti|ks')->name('heroes_list');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_hero', [HeroController::class, "delete_hero"])->name('delete_hero');
Route::middleware(['auth:sanctum', 'verified'])->get('/heroes/{site}/more/{id}', [HeroController::class, "edit_hero"])->where('site', 'pguty|psuti|ks')->name('edit_hero');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_hero", [HeroController::class, "update_hero"])->name('update_hero');

Route::middleware(['auth:sanctum', 'verified'])->get('/unit', [UnitController::class, 'index'])->name('unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/units/{site}/more/{id}', [UnitController::class, "edit_unit"])->where('site', 'pguty|psuti|ks')->name('edit_unit');
Route::middleware(['auth:sanctum', 'verified'])->get('/units/{site}', [UnitController::class, "units_list"])->where('site', 'pguty|psuti|ks')->name('units_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_unit', [UnitController::class, "search_unit"])->name('search_unit');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_unit', [UnitController::class, "delete_unit"])->name('delete_unit');

Route::middleware(['auth:sanctum', 'verified'])->get('/event_file', [EventController::class, 'index_file'])->name('event_file');
Route::middleware(['auth:sanctum', 'verified'])->post('/add_event_file', [EventController::class, 'add_event_file'])->name('add_event_file');
Route::middleware(['auth:sanctum', 'verified'])->get('/event', [EventController::class, 'index'])->name('event');
Route::middleware(['auth:sanctum', 'verified'])->get('/events/{site}', [EventController::class, 'events_list'])->where('site', 'pguty|psuti|ks')->name('events_list');
Route::middleware(['auth:sanctum', 'verified'])->post('/add_event', [EventController::class, 'add_event'])->name('add_event');
Route::middleware(['auth:sanctum', 'verified'])->get('/events/{site}/more/{id}', [EventController::class, "edit_event"])->where('site', 'pguty|psuti|ks')->name('edit_event');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_event", [EventController::class, "update_event"])->name('update_event');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_event', [EventController::class, "search_event"])->name('search_event');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_event', [EventController::class, "delete_event"])->name('delete_event');

Route::middleware(['auth:sanctum', 'verified'])->get('/graduate', [GraduateController::class, "index"])->name('graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/graduates/{site}', [GraduateController::class, "graduates_list"])->where('site', 'pguty|psuti|ks')->name('graduates_list');
Route::middleware(['auth:sanctum', 'verified'])->post("/add_graduate", [GraduateController::class, "add_graduate"])->name('add_graduate');
Route::middleware(['auth:sanctum', 'verified'])->get('/graduates/{site}/more/{id}', [GraduateController::class, "more_graduate"])->where('site', 'pguty|psuti|ks')->name('more_graduate');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_graduate', [GraduateController::class, "delete_graduate"])->name('delete_graduate');

Route::middleware(['auth:sanctum', 'verified'])->get('/pages', [PageController::class, "pages_list"])->name('pages_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/pages/more/{alias}', [PageController::class, "edit_page"])->name('edit_page');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_page", [PageController::class, "update_page"])->name('update_page');

Route::middleware(['auth:sanctum', 'verified'])->get('/users', [UserController::class, "users_list"])->name('users_list');
Route::middleware(['auth:sanctum', 'verified'])->get('/users/more/{id}', [UserController::class, "edit_user"])->name('edit_user');
Route::middleware(['auth:sanctum', 'verified'])->post("/update_user", [UserController::class, "update_user"])->name('update_user');
Route::middleware(['auth:sanctum', 'verified'])->get('/search_user', [UserController::class, "search_user"])->name('search_user');
Route::middleware(['auth:sanctum', 'verified'])->post('/delete_user', [UserController::class, "delete_user"])->name('delete_user');