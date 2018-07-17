<?php

use App\Team\Job;
use App\Members\Member;

use Illuminate\Database\Seeder;

class MembersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create(config('app.locale'));

        // Default admin user
        $admin = Member::create([
            'name'   => 'Administrador',
            'email'  => 'no@email.com',
            'password' => bcrypt('admin'),
            'ra'     => '000000',
            'course' => 'Administrador do sistema',
            'admission_year' => '2008'
        ]);
        $admin->approve();
        $admin->assign('admin');

        // Random active members
        $members = factory(Member::class, 12)->create();

        // Assign a job (or two) to the active members
        $jobs = Job::all();
        $ids = $jobs->keys()->all();
        foreach ($members as $member) {
            $member->addJob($jobs[$faker->unique($reset = true)->randomElement($ids)]);
            if ($faker->boolean(30))
                $member->addJob($jobs[$faker->unique()->randomElement($ids)]); // unique second job
        }
    }
}
