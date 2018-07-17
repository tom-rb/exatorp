<?php

namespace App\Students\Controllers;

use App\Infrastructure\Http\Controller;

class StudentHomeController extends Controller
{
    public function index()
    {
        return view('student.home');
    }
}
