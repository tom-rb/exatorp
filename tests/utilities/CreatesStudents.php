<?php

namespace Tests\utilities;


trait CreatesStudents
{

    protected function signInStudent($student = null)
    {
        $student = $student ?: create(\App\Students\Student::class);

        $this->actingAs($student, 'student');

        return $this;
    }

}