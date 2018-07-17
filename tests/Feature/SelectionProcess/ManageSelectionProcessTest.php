<?php

namespace Tests\Feature;

use App\Members\Member;
use App\SelectionProcess\SelectionProcess;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageSelectionProcessTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function draft()
    {
        return true;
    }
}
