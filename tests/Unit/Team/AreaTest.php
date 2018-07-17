<?php

namespace Tests\Unit;

use App\Team\Job;
use App\Team\Area;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AreaTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_slug_representation()
    {
        $area = new Area(['name' => 'My Árêa']);
        $this->assertEquals('my-area', $area->slug);

        // Doesn't override custom slug
        $area = create(Area::class, ['name' => 'Niño area', 'slug' => 'nino']);
        $this->assertEquals('nino', $area->slug);
    }

    /** @test */
    public function it_has_jobs_associated_to_it()
    {
        $area = create(Area::class);
        $job = create(Job::class, ['area_id' => $area->id]);

        $this->assertTrue($area->jobs->contains($job));
    }
}
