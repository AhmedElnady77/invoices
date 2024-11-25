<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\CustomersReportController;
use App\Http\Controllers\FawateerArchiveController;
use App\Http\Controllers\FawateerAttachmentsController;
use App\Http\Controllers\FawateerController;
use App\Http\Controllers\FawateerDetailsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoicesReportController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::controller(LoginRegisterController::class)->group(function(){
Route::get('/register','register')->name('register');
Route::post('/store','store')->name('store');
Route::get('/login', 'login')->name('login');
Route::post('/authenticate', 'authenticate')->name('authenticate');
Route::get('/dashboard', 'dashboard')->name('dashboard');
Route::post('/logout', 'logout')->name('logout');
});


Route::get('/admin',[AdminController::class,'index']);
Route::resource('/fawateer',FawateerController::class);
Route::resource('/sections',SectionController::class);
Route::resource('/products',ProductsController::class);
Route::resource('/fawateerAttachments',FawateerAttachmentsController::class);
Route::get('/section/{id}',[FawateerController::class,'getproducts'])->name('section');
Route::get('/FawateerDetails/{id}',[FawateerDetailsController::class,'edit']);
Route::get('/view_file/{invoice_number}/{file_name}',[FawateerDetailsController::class,'openfile'])->name('view_file');
Route::get('/download/{invoice_number}/{file_name}',[FawateerDetailsController::class,'getfile']);
Route::post('/delete_file',[FawateerDetailsController::class,'destroy'])->name('delete_file');
Route::get('/edit_fawateer/{id}',[FawateerController::class,'edit']);
Route::get('/Status_show/{id}',[FawateerController::class,'show'])->name('Status_show');
Route::post('/Status_Update/{id}',[FawateerController::class,'Status_Update'])->name('Status_Update');
Route::get('/fawateer_paid',[FawateerController::class,'fawateer_paid']);
Route::get('/fawateer_unpaid',[FawateerController::class,'fawateer_unpaid']);
Route::get('/fawateer_partial',[FawateerController::class,'fawateer_partial']);
Route::get('/fawateer_archive',[FawateerArchiveController::class,'index']);
Route::post('/fawateer_archive',[FawateerArchiveController::class,'update']);
Route::get('/fawateer_archive',[FawateerArchiveController::class,'destroy']);
Route::get('/Print_Fawateer/{id}',[FawateerController::class,'Print_Fawateer']);


Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);


});

Route::get('invoices_report',[InvoicesReportController::class,'index']);
Route::post('/Search_invoices',[InvoicesReportController::class,'search_invoices']);
Route::get('customers_report',[CustomersReportController::class,'index']);
Route::post('/Search_customers',[CustomersReportController::class,'Search_customers']);


Route::get('/dashboard',[HomeController::class,'index']);

Route::get('/MarkAsRead_all',[FawateerController::class,'MarkAsRead_all'])->name('MarkAsRead_all');
