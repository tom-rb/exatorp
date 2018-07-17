<?php

use App\SelectionProcess\HowDidYouHearOption;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\Question;
use App\SelectionProcess\SelectionProcess;

/**
 * Selection Process
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SelectionProcess::class, function (Faker\Generator $faker) {
    // Create a closed selection process that will open in the future (in 15 days is guaranteed to be opened,
    // tests cases use this assumption)
    return [
        'open_date' => $faker->dateTimeBetween('+1 day', '+10 days'),
        'close_date' => function ($inputs) {
            return \Carbon\Carbon::instance($inputs['open_date'])->addMonth();
        },
        'questions' => [
            new Question(rtrim($faker->sentence(), '.').'?', $faker->sentence()),
            new Question(rtrim($faker->sentence(), '.').'?', $faker->sentence()),
            new Question(rtrim($faker->sentence(), '.').'?', $faker->sentence()),
            ],
    ];
});

$factory->state(SelectionProcess::class, 'no_questions', function() {
    return [
        'questions' => []
    ];
});

$factory->state(SelectionProcess::class, 'opened', function(Faker\Generator $faker) {
    return [
        'open_date' => $faker->dateTimeBetween('-10 day', '-1 days'),
        'close_date' => $faker->dateTimeBetween('+10 days', '+1 month'),
    ];
});

/**
 * Member Application
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(MemberApplication::class, function (Faker\Generator $faker) {
    return [
        'member_id' => function () {
            return factory(App\Members\Member::class)->states(['candidate'])->create()->id;
        },
        'selection_process_id' => function () {
            return factory(SelectionProcess::class)->create()->id;
        },
        'first_area_id' => function ($inputs) {
            SelectionProcess::find($inputs['selection_process_id'])->addArea(
                $area = factory(App\Team\Area::class)->create()
            );
            return $area->id;
        },
        'second_area_job' => function ($inputs) use ($faker) {
            if ($faker->boolean()) {
                SelectionProcess::find($inputs['selection_process_id'])->addJob(
                    $job = factory(App\Team\Job::class)->create()
                );
                return $job->id;
            }
        },
        'second_area_id' => function ($inputs) {
            $job = App\Team\Job::find($inputs['second_area_job']);
            return $job ? $job->area_id : null;
        },
        'answers' => function ($inputs) use ($faker) {
            $answers = [];
            foreach (SelectionProcess::find($inputs['selection_process_id'])->questions as $q) {
                $answers[] = $faker->paragraph();
            }
            return $answers;
        },
        'how_did_you_hear' => function ($inputs) use ($faker) {
            return factory(HowDidYouHearOption::class, 2)->create()->pluck('id');
        },
        'trying_first_option' => true,
    ];
});

$factory->state(MemberApplication::class, 'no_member', function(Faker\Generator $faker) {
    return [
        'member_id' => null
    ];
});

$factory->state(MemberApplication::class, 'with_second_job', function(Faker\Generator $faker) {
    return [
        'second_area_job' => function ($inputs) use ($faker) {
            SelectionProcess::find($inputs['selection_process_id'])->addJob(
                $job = factory(App\Team\Job::class)->create()
            );
            return $job->id;
        },
        'second_area_id' => function ($inputs) {
            return App\Team\Job::find($inputs['second_area_job'])->area_id;
        },
    ];
});

/**
 * How did you hear Option
 *
 * @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(HowDidYouHearOption::class, function (Faker\Generator $faker) {
    return [
        'description' => 'By ' . $faker->sentence(3),
    ];
});