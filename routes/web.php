<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Yajra\DataTables\Facades\DataTables;

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

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees')->middleware('auth');

Route::get('/positions', [PositionController::class, 'index'])->name('positions')->middleware('auth');

Route::get('/autocomplete', [EmployeeController::class, 'autocomplete'])->name('autocomplete');

Route::get('/autoc-position', [PositionController::class, 'autocomplete'])->name('autoc-position');


//------------------------------------Employees Routes
Route::post(
    '/employees/submit',
    [EmployeeController::class, 'store']
)
    ->name('employee-add')->middleware('auth');

Route::get(
    '/employees/{id}/delete',
    [EmployeeController::class, 'destroy']
)
    ->name('employee-delete')->middleware('auth');

Route::get(
    '/employees/{id}/edit',
    [EmployeeController::class, 'edit']
)
    ->name('employee-edit');

Route::post(
    '/employees/{id}/update',
    [EmployeeController::class, 'update']
)
    ->name('employee-update')->middleware('auth');


//-------------------------------------Position Routes
Route::post(
    '/positions/submit',
    [PositionController::class, 'store']
)
    ->name('position-add')->middleware('auth');

Route::get(
    '/positions/{id}/delete',
    [PositionController::class, 'destroy']
)
    ->name('position-delete')->middleware('auth');

Route::get(
    '/positions/{id}/edit',
    [PositionController::class, 'edit']
)
    ->name('position-edit');

Route::post(
    '/positions/{id}/update',
    [PositionController::class, 'update']
)
    ->name('position-update')->middleware('auth');
