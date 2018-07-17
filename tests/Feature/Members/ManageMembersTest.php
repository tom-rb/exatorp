<?php

namespace Tests\Feature\Members;

use App\Members\Member;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageMembersTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function unauthorized_members_cannot_dismiss_other_members()
    {
        $member = create(Member::class);

        $this->withExceptionHandling()
            ->signIn()
            ->postJson(route('member.dismiss', $member))
            ->assertStatus(403);

        $this->assertTrue($member->fresh()->isActive());
    }

    /** @test */
    public function authorized_members_can_dismiss_other_members()
    {
        $member = create(Member::class);

        $this->signIn($this->newMemberCan('manage-members'))
            ->postJson(route('member.dismiss', $member))
            ->assertJson(['message' => 'Desligado(a) ' . $member->name]);

        $this->assertTrue($member->fresh()->isFormer());
    }
}
