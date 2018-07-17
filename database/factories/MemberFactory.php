<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Members\Member::class, function (Faker\Generator $faker) {
    return [
        'name' => str_replace('.', '', $faker->name), // remove dots from Sra. Jr. etc
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(str_random(10)),
        'status' => App\Members\Member::ACTIVE,
        'ra' => $faker->numerify('######'),
        'course' => title_case($faker->words($nbWords = 3, $asText = true)),
        'admission_year' => $faker->numberBetween(1970, 2020),
        'phones' => [ preg_replace('/[^\d ]/', '', $faker->phoneNumber) ],
        'availability' => new \App\Members\LessonAvailability([1 => [1, 2], 2 => [2]]),
    ];
});

$factory->state(App\Members\Member::class, 'candidate', function() {
    return [
        'status' => App\Members\Member::CANDIDATE
    ];
});

$factory->state(App\Members\Member::class, 'former', function() {
    return [
        'status' => App\Members\Member::FORMER_MEMBER
    ];
});
