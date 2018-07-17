<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Students\Student::class, function (Faker\Generator $faker) {
    return [
        'name' => str_replace('.', '', $faker->name), // remove dots from Sra. Jr. etc
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(str_random(10)),
    ];
});
