<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\User;
use App\Testimonial;
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
    $dokter = User::where('role','dokter')->get();
    $testimonial = Testimonial::all();
    return view('welcome',compact('dokter','testimonial'));
}) ->name('welcome');

// welcome
Route::get('register', 'PasienController@register')->name('register.pasien');
Route::post('register/store', 'PasienController@storelanding')->name('storelanding.pasien');
Route::get('isitestimonial', 'PasienController@testimonial')->name('isi.testimonial');
Route::get('register/konfirmasi', 'PasienController@konfirmasiBayar')->name('konfirmasi.bayar');
Route::post('register/konfirmasi/store', 'PasienController@storeKonfirmasiBayar')->name('store.konfirmasiBayar');
Route::post('testimonial/store', 'PasienController@storeTestimonial')->name('store.testimonial');

// buat antrian (pasien)
Route::get('appointment', function(){
    $dokter = User::where('role','dokter')->get();
    return view('appointment',compact('dokter'));
})->name('appointment');
Route::post('appointment/store', 'AntrianController@store')->name('store.antrian');


// antrian (pasien)
Route::get('antrian/{user:username}', 'AntrianController@index')->name('list.antrian');

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false
]);

Route::get('dashboard', 'DashboardController@index')->name('dashboard');

// antrian (dokter)
Route::post('{user:id}/panggil','AntrianController@panggil')->name('panggil.antrian');
Route::post('{user:id}/panggil/skipped','AntrianController@panggilSkipped')->name('panggil.skipped.antrian');
Route::post('{antrian:id}/periksa','AntrianController@periksa')->name('periksa.antrian');
Route::post('{antrian:id}/lewati','AntrianController@lewati')->name('lewati.antrian');
Route::post('{antrian:id}/selesai','AntrianController@selesai')->name('selesai.antrian');
Route::post('{user:id}/hapus', 'AntrianController@hapusAntrian')->name('hapus.antrian');

// Buka/tutup praktek
Route::post('praktek/buka','DashboardController@bukaPraktek')->name('buka.praktek');
Route::post('praktek/tutup', 'DashboardController@tutupPraktek')->name('tutup.praktek');


Route::group(['middleware' => ['auth', 'cekrole:admin']], function () {
    Route::get('dokter', 'Admin\DokterController@index')->name('dokter');
    Route::get('dokter/create', 'Admin\DokterController@create')->name('create.dokter');
    Route::post('dokter/store', 'Admin\DokterController@store')->name('store.dokter');
    Route::get('dokter/{user:id}/edit', 'Admin\DokterController@edit')->name('edit.dokter');
    Route::patch('dokter/{user:id}/edit', 'Admin\DokterController@update')->name('update.dokter');
    Route::delete('dokter/{user:id}/delete', 'Admin\DokterController@destroy')->name('delete.dokter');

    Route::get('staff', 'Admin\StaffController@index')->name('staff');
    Route::get('staff/create', 'Admin\StaffController@create')->name('create.staff');
    Route::post('staff/store', 'Admin\StaffController@store')->name('store.staff');
    Route::get('staff/{user:id}/edit', 'Admin\StaffController@edit')->name('edit.staff');
    Route::patch('staff/{user:id}/edit', 'Admin\StaffController@update')->name('update.staff');
    Route::delete('staff/{user:id}/delete', 'Admin\StaffController@destroy')->name('delete.staff');

    Route::get('export/dokter', 'Admin\DokterController@exportExcel')->name('export.dokter');
    Route::get('export/staff', 'Admin\StaffController@exportExcel')->name('export.staff');
    Route::get('export/pasien', 'PasienController@exportExcel')->name('export.pasien');
});

Route::group(['middleware' => ['auth','cekrole:admin,staff,dokter']], function () {
    // pasien
    Route::get('pasien', 'PasienController@index')->name('pasien');
    Route::get('pasien/create', 'PasienController@create')->name('create.pasien');
    Route::post('pasien/store', 'PasienController@store')->name('store.pasien');
    Route::get('pasien/{pasien:id}/edit', 'PasienController@edit')->name('edit.pasien');
    Route::patch('pasien/{pasien:id}/edit', 'PasienController@update')->name('update.pasien');
    Route::delete('pasien/{pasien:id}/delete', 'PasienController@destroy')->name('delete.pasien');
    Route::post('pasien/{pasien:id}/konfirmasi', 'PasienController@konfirmasi')->name('konfirmasi.pasien');
    Route::get('pasien/{pasien:id}/riwayat', 'RiwayatController@index') ->name('riwayat.pasien');
    Route::get('pasien/riwayat/create/{pasien:id}', 'RiwayatController@create')->name('create.riwayat.pasien');
    Route::post('pasien/riwayat/store', 'RiwayatController@store')->name('store.riwayat.pasien');
    Route::get('print/{pasien:id}', 'RiwayatController@print')->name('print.riwayat');

    // testimonial
    Route::get('testimonial','PasienController@listTestimonial')->name('list.testimonial');
    Route::delete('testimonial/{testimonials:id}/delete', 'PasienController@destroyTestimonial')->name('delete.testimonial');
});

