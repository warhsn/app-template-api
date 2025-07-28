<?php

use App\Http\Controllers\Auth\LoadAuthUser;
use App\Http\Controllers\Dashboard\LoadAdminDashboard;
use App\Http\Controllers\User\CreateUser;
use App\Http\Controllers\User\ExportUserXlsx;
use App\Http\Controllers\User\LoadAllCustomersUserList;
use App\Http\Controllers\User\LoadRoles;
use App\Http\Controllers\User\LoadUser;
use App\Http\Controllers\User\PaginatedUsers;
use App\Http\Controllers\User\ResetPassword;
use App\Http\Controllers\User\UpdateAuthUser;
use App\Http\Controllers\User\UpdateUser;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

Route::middleware('api')->group(function () {
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('reset-password', [NewPasswordController::class, 'store']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('user', LoadAuthUser::class)->name('auth.user');
    Route::post('profile', UpdateAuthUser::class)->name('auth.updateUser');

    Route::get('dashboard/admin', LoadAdminDashboard::class)->name('dashboard.admin');

    // Users
    Route::get('users', PaginatedUsers::class)->name('users.paginated');
    Route::post('users', CreateUser::class)->name('users.create');
    Route::get('users/export-xlsx', ExportUserXlsx::class)->name('users.exportXlsx');
    Route::get('users/{user}', LoadUser::class)->name('users.load');
    Route::put('users/{user}', UpdateUser::class)->name('users.update');
    Route::post('users/{user}/password', ResetPassword::class)->name('users.password');

    // Meta Data
    Route::get('user-roles', LoadRoles::class)->name('meta.userRoles');
    Route::get('customer-user-list', LoadAllCustomersUserList::class)->name('meta.customerUserList');
});
