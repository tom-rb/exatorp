<?php

use App\Team\Job;
use App\Team\Area;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;

class JobsSeeder extends Seeder
{
    public function run()
    {
        // Default teaching jobs
        $areas = collect(['Física', 'Língua Portuguesa', 'Matemática', 'Química']);
        $areas->transform(function ($area) {
            return Area::create(['name' => $area]);
        });

        $jobs = collect([
            ['name' => 'Monitor', 'role_id' => Role::whereName('membro')->first()->id,
                'description' => 'Auxilia nas atividades de ensino'],
            ['name' => 'Professor', 'role_id' => Role::whereName('prof')->first()->id,
                'description' => 'Ministra as aulas'],
            ['name' => 'Coordenador', 'role_id' => Role::whereName('coord')->first()->id,
                'description' => 'Responsável pela coordenação da área'],
            ]);
        foreach ($areas as $area) {
            $jobs->each(function ($job) use ($area) {
                $job['area_id'] = $area->id;
                Job::create($job);
            });
        }

        // Default organization jobs
        $orgArea = Area::create(['name' => 'Organização']);
        collect([
            ['name' => 'Coordenador Geral', 'role_id' => Role::whereName('coord-geral')->first()->id,
                'description' => 'Responsável pela coordenação geral'],
            ['name' => 'Arte', 'role_id' => Role::whereName('membro')->first()->id,
                'description' => 'Identidade visual, cartazes, camisetas, certificados, etc.'],
            ['name' => 'Suporte', 'role_id' => Role::whereName('membro')->first()->id,
                'description' => 'Atividades de suporte à equipe, como organização de atividades, controle de biblioteca, mídias sociais, etc.'],
            ['name' => 'Informática', 'role_id' => Role::whereName('membro')->first()->id,
                'description' => 'Manutenção do fórum interno, apoio de TI, elaboração de ferramentas para a equipe'],
        ])->each(function ($job) use ($orgArea) {
            $job['area_id'] = $orgArea->id;
            Job::create($job);
        });
    }
}
