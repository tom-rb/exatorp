<?php

namespace Tests\Unit;

use App\Team\Job;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Validator;

class CustomValidatorTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_validates_alpha_spaces()
    {
        Validator::make(
            ['my_input' => 'I have only alpha chars and spaces'],
            ['my_input' => 'alpha_spaces']
        )->validate();

        $this->assertFalse(
            Validator::make(
                ['my_input' => 'I have not only alpha chars and spaces!'],
                ['my_input' => 'alpha_spaces']
            )->passes()
        );
    }

    /** @test */
    public function it_validates_exists_where()
    {
        $job = create(Job::class);

        Validator::make(
            ['area' => $job->area_id, 'my_job' => $job->id],
            ['my_job' => 'exists_where:jobs,id,area_id,&area']
        )->validate();

        $this->assertFalse(
            Validator::make(
                ['area' => $job->area_id + 99, 'my_job' => $job->id],
                ['my_job' => 'exists_where:jobs,id,area_id,&area']
            )->passes()
        );
    }

    /** @test */
    public function it_validates_hashed_values()
    {
        Validator::make(
            ['my_input' => 'correct_password'],
            ['my_input' => 'hash:'.bcrypt('correct_password')]
        )->validate();

        $this->assertFalse(
            Validator::make(
                ['my_input' => 'correct_password'],
                ['my_input' => 'hash:'.bcrypt('wrong_password')]
            )->passes()
        );
    }
}
