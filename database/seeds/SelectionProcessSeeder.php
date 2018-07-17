<?php

use Faker\Factory as Faker;
use App\SelectionProcess\HowDidYouHearOption;
use App\SelectionProcess\MemberApplication;
use App\SelectionProcess\SelectionProcess;
use App\Team\Area;
use Illuminate\Database\Seeder;

class SelectionProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Some questions
        HowDidYouHearOption::create(['description' => 'Indicação de amigo']);
        HowDidYouHearOption::create(['description' => 'Página do Curso no Facebook']);
        HowDidYouHearOption::create(['description' => 'Grupos no Facebook']);
        HowDidYouHearOption::create(['description' => 'Site do Curso']);
        HowDidYouHearOption::create(['description' => 'Já fui aluno']);
        HowDidYouHearOption::create(['description' => 'Outro']);

        // Create past processes and a currently opened one
        $processes = factory(SelectionProcess::class, $n = 2)->make()->transform(function ($process, $i) use ($n) {
            $monthsBehind = ($n - $i) * 6;
            $process->open_date = $process->open_date->subMonths($monthsBehind);
            $process->close_date = $process->close_date->subMonths($monthsBehind);
            return $process;
        })->each->save();
        $processes->push(factory(SelectionProcess::class)->states('opened')->create());

        // Fill them
        $processes->each(function (SelectionProcess $process) {

            // Register areas, some with specific jobs
            $areas = Area::inRandomOrder()->with('jobs')->take($a = 4)->get()
                ->each(function ($area) use ($process) {
                    if (Faker::create()->boolean())
                        $process->addArea($area);
                    else {
                        $jobs = $area->jobs->random(3);
                        $process->addJob($jobs->first());
                        $process->addJob($jobs->last());
                    }
            });

            $howDids = HowDidYouHearOption::all()->pluck('id')->toArray(); // wait for it...

            // Register 4 candidates/area
            for ($i = 0; $i < 4*$a; ++$i)
            {
                // Randomize candidate selection
                $selected = collect([
                        $areas->random()->id,                                      // first area
                        Faker::create()->boolean() ? $areas->random()->id : null   // second (optional) area
                    ])->map(function ($area) use ($process) {
                        $jobs = $area ? $process->jobsForArea($area) : collect();
                        return [
                            $area,
                            $jobs->isNotEmpty() ? $jobs->random()->id : null
                        ];
                    })->flatten();

                factory(MemberApplication::class)->create([
                    'selection_process_id' => $process->id,
                    'first_area_id' => $selected[0],
                    'first_area_job' => $selected[1],
                    'second_area_id' => $selected[2],
                    'second_area_job' => $selected[3],
                    'how_did_you_hear' => Faker::create()->randomElements($howDids, 2), // ...here they are
                ]);
            }
        });
    }
}
