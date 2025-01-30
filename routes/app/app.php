<?php

use App\Http\Controllers\App\Roles\RoleController;
use App\Http\Controllers\App\Users\UserController;
use App\Http\Controllers\App\Users\AppUserController;
use App\Http\Controllers\App\Users\UserRoleController;
use App\Http\Controllers\App\Users\UserSocialLinkController;
use App\Http\Controllers\App\Auth\AuthenticateUserController;
use App\Http\Controllers\App\Notification\NotificationController;
use App\Http\Controllers\App\Settings\ReCaptchaSettingController;
use App\Http\Controllers\App\PaymentMethod\StripeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\PaymentMethodController;

Route::get('check', function (){
    dd(redirect(config('app.url') . '/payment-view'));
});
//Route::get('/user/registration', [AuthenticateUserController::class, 'registerView']);
Route::resource('transactions', TransactionController::class);
Route::get('transaction/withdrawal', [TransactionController::class, 'withdrawal'])->name('transaction.withdrawal');
Route::get('transaction/statu', [TransactionController::class, 'status'])->name('transaction.status');
Route::get('users/refered', [ReferralController::class, 'referrals'])->name('user.refered');
Route::get('transactions/status/withdrawal', [TransactionController::class, 'statusWithdrawal'])->name('transaction.statusWithdrawal');
Route::get('transactions/admin', [TransactionController::class, 'indexAdmin'])->name('transaction.indexAdmin');
Route::post('create/transaction', [TransactionController::class, 'createTransaction'])->name('transaction.createTransaction');
Route::post('edit/transaction/{id}', [TransactionController::class, 'editTransaction'])->name('transaction.editTransaction');
Route::patch('edit/transaction2/{id}', [TransactionController::class, 'editTransaction2'])->name('transaction.editTransaction2');
Route::get('account/transaction', [TransactionController::class, 'account'])->name('transaction.accountTransaction');
Route::get('users/comisiones/{id}', [TransactionController::class, 'comisiones'])->name('user.comisiones');
Route::view('/my-profile', 'user.profile');
Route::get('/user/encrypted-id', [TransactionController::class, 'getEncryptedUserId'])->name('user.id');
Route::get('transactions/status/withdrawal', [TransactionController::class, 'statusWithdrawal'])->name('transaction.statusWithdrawal');
Route::get('transactions/admin', [TransactionController::class, 'indexAdmin'])->name('transaction.indexAdmin');
Route::post('create/transaction', [TransactionController::class, 'createTransaction'])->name('transaction.createTransaction');
Route::post('edit/transaction/{id}', [TransactionController::class, 'editTransaction'])->name('transaction.editTransaction');
Route::patch('edit/transaction2/{id}', [TransactionController::class, 'editTransaction2'])->name('transaction.editTransaction2');
Route::get('account/transaction', [TransactionController::class, 'account'])->name('transaction.accountTransaction');
Route::view('/transacciones', 'balance.balance')->name('balance.balance');
Route::view('/depositos', 'deposito.deposito')->name('deposito.deposito');
Route::view('/deposit/confirmation', 'deposit.confirmation')->name('deposit.confirmation');

Route::view('/retiros', 'retiro.retiro')->name('retiro.retiro');

Route::view('/transacciones/admin', 'balance.admin')->name('balance.admin');
Route::view('/user/referidos', 'refered.index')->name('userRefered.index');
Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment.accountTransaction');
Route::view('/users-and-roles', 'user-roles.user-roles')->name('user-role.list');

//User
Route::resource('user-list', UserController::class);

// update user name
Route::post('/update-user-name/{id}', [UserController::class, 'updateUserName']);

// role
Route::get('users/{role}', [RoleController::class, 'getUsersByRole']);

// user
Route::get('all-users', [UserController::class, 'getUsers']);

//users
Route::get('get/users', [AppUserController::class, 'index']);

// role_user
Route::post('attach-user/{role}', [UserRoleController::class, 'store']);
// Route::delete('attach-user/{id}',[UserRoleController::class,'destroy']);

// Sample Pages Routes
Route::view('/blank-page', 'sample-pages.sample');

// Payment Methods
Route::view('/payment-view', 'sample-pages.payment-view');

// roles
Route::get('all-roles', [RoleController::class, 'getAllRoles']);

// ALl Notifications page
Route::get('/all-notifications', [NotificationController::class, 'view']);

Route::get('login-user', [AuthenticateUserController::class, 'show'])
    ->name('user.login-user');

Route::post('change-social-link', [UserSocialLinkController::class, 'update'])
    ->name('user.change-social-link');

//get captcha
Route::get('/get-re-captcha-setting', [ReCaptchaSettingController::class, 'getReCaptchaSettings'])
    ->name('re-captcha-settings.get');


Route::get('stripe', [StripeController::class, 'stripe']);
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');