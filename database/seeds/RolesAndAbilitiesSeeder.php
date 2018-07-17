<?php

use Silber\Bouncer\Database\Models;
use Illuminate\Database\Seeder;

class RolesAndAbilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $this->createRole(['name' => 'admin',
            'title' => 'Administrador do sistema']);
        $this->createRole(['name' => 'coord-geral',
            'title' => 'Coordenador Geral']);
        $this->createRole(['name' => 'coord',
            'title' => 'Coordenador de disciplina']);
        $this->createRole(['name' => 'prof',
            'title' => 'Professor']);
        $this->createRole(['name' => 'membro',
            'title' => 'Membro']);

        // Admin role have all abilities
        Bouncer::allow('admin')->to('*');

        // Create abilities
        $this->createAbility(['name' => 'approve-candidates',
            'title' => 'Aprovar, colocar em espera e recusar candidatos.']);
        $this->createAbility(['name' => 'reset-candidates',
            'title' => 'Desfazer a aprovação/reprovação de candidatos.']);
        $this->createAbility(['name' => 'manage-members',
            'title' => 'Trocar cargos e desligar membros.']);
        $this->createAbility(['name' => 'manage-member-documents',
            'title' => 'Controlar documentação e certificados de membros.']);
        $this->createAbility(['name' => 'manage-selection-processes',
            'title' => 'Criar e editar processos seletivos de membros.']);
        $this->createAbility(['name' => 'administrate-members',
            'title' => 'Editar e excluir membros.']);

        // Assign permissions
        Bouncer::allow('coord-geral')->to('approve-candidates');
        Bouncer::allow('coord-geral')->to('reset-candidates');
        Bouncer::allow('coord-geral')->to('manage-members');
        Bouncer::allow('coord-geral')->to('manage-member-documents');
        Bouncer::allow('coord-geral')->to('manage-selection-processes');
        Bouncer::allow('coord')->to('approve-candidates');
        Bouncer::allow('coord')->to('manage-members');
        Bouncer::allow('coord')->to('manage-member-documents');
    }

    private function createAbility($attributes)
    {
        return call_user_func(get_class(Models::ability()) . '::create', $attributes);
    }

    private function createRole($attributes)
    {
        return call_user_func(get_class(Models::role()) . '::create', $attributes);
    }
}
