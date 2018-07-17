<?php
Route::get('/', 'WelcomeController@index')->name('member.welcome');

// Members authentication and password recover
Route::group(['namespace' => 'Members\Controllers\Auth', 'prefix' => 'membros'], function() {
    // Authentication Routes
    Route::post('entrar', 'LoginController@login')->name('member.auth.login');
    Route::get('sair', 'LoginController@logout')->name('member.auth.logout');

    // Password Reset Routes
    Route::get('senha/troca', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('senha/email', 'ForgotPasswordController@sendResetLinkEmail')->name('member.auth.password.email');
    Route::get('senha/troca/{token?}', 'ResetPasswordController@showResetForm')->name('member.auth.password.reset');
    Route::post('senha/troca', 'ResetPasswordController@reset');
});

// Students authentication and password recover
Route::group(['namespace' => 'Students\Controllers\Auth', 'prefix' => 'alunos'], function() {
    // Authentication Routes
    Route::get('entrar', 'LoginController@showLoginForm')->name('student.auth.login');
    Route::post('entrar', 'LoginController@login');
    Route::get('sair', 'LoginController@logout')->name('student.auth.logout');

    // Password Reset Routes
    Route::get('senha/troca', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('senha/email', 'ForgotPasswordController@sendResetLinkEmail')->name('student.auth.password.email');
    Route::get('senha/troca/{token?}', 'ResetPasswordController@showResetForm')->name('student.auth.password.reset');
    Route::post('senha/troca', 'ResetPasswordController@reset');
});

// Member Selection Processes public routes
Route::group(['namespace' => 'SelectionProcess\Controllers'], function()
{
    // Member selection process
    Route::get('processos-seletivos/{process}/aplicar', 'MemberApplicationController@create')->name('selection-process.application.create');
    Route::post('processos-seletivos/{process}/aplicar', 'MemberApplicationController@store');
});


// Routes that admins can impersonate other users
Route::group(['middleware' => ['auth:member', 'impersonate']], function()
{
    // Logged home page
    Route::get('/inicio', 'Members\Controllers\HomeController@index')->name('member.home');

    // Members
    Route::resource('membros', 'Members\Controllers\MembersController', [
        'except' => ['create', 'store'],
        'parameters' => ['membros' => 'member'],
        'names' => [
            'index' => 'member.index',
            'show' => 'member.show',
            'edit' => 'member.edit',
            'update' => 'member.update',
            'destroy' => 'member.destroy'
        ]
    ]);
    Route::post('membros/{member}/desligar', 'Members\Controllers\MembersController@dismiss')->name('member.dismiss');

    // Members Selection Process
    Route::group(['prefix' => 'processos-seletivos', 'namespace' => 'SelectionProcess\Controllers'], function()
    {
        Route::get('/', 'SelectionProcessController@showLatest')->name('selection-process.index');
        Route::get('/{process}', 'SelectionProcessController@show')->name('selection-process.show');

        Route::get('/{process}/candidatos', 'CandidatesController@index')->name('selection-process.application.index');
        Route::get('/{process}/candidatos/{member}', 'CandidatesController@show')->name('selection-process.application.show');
        Route::patch('/{process}/candidatos/{member}', 'CandidatesController@update');

        Route::get('/{process}/csv', 'ExportCsvController@export')->name('selection-process.csv');
    });

    // Administrator routes
    Route::get('membros/{member}/atuar-como', 'Administration\Impersonation\ImpersonateController@impersonate')->name('impersonate.start');
    Route::get('parar-atuar', 'Administration\Impersonation\ImpersonateController@stopImpersonate')->name('impersonate.stop');
    Route::get('decompose', '\Lubusin\Decomposer\Controllers\DecomposerController@index')->middleware('admin');

}); // end impersonation

// Routes that admins can impersonate students
Route::group(['middleware' => ['auth:student', 'impersonate']], function() {

    // Student routes
    Route::group(['prefix' => 'meu'], function () {
        require(__DIR__ . '/_students.php');
    });
});
