<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Team\Area::class, function (Faker\Generator $faker) {
    return [
        'name' => ucfirst($faker->unique()->word) . ' Area',
    ];
});

$factory->define(App\Team\Job::class, function (Faker\Generator $faker) {
    return [
        'name' => ucfirst($faker->unique()->word) . ' Job',
        'area_id' => function () {
            return factory(App\Team\Area::class)->create()->id;
        },
        'role_id' => function () {
            return factory(Silber\Bouncer\Database\Role::class)->create()->id;
        },
        'description' => $faker->sentence(),
    ];
});

$factory->define(Silber\Bouncer\Database\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => ucfirst($faker->unique()->word) . '-role',
        'title' => function ($inputs) {
            // Make a title based on the name
            return title_case(str_replace('-', ' ', $inputs['name']));
        },
    ];
});