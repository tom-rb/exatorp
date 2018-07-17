<?php

// Logged home page
Route::get('/inicio', 'Students\Controllers\StudentHomeController@index')->name('student.home');
