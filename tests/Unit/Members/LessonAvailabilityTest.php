<?php

namespace Tests\Unit;

use App\Members\LessonAvailability;

use Tests\TestCase;

class LessonAvailabilityTest extends TestCase
{
    private $data = [2 => [1, 2], 4 => [2], 'obs' => 'my obs'];

    /**
     * @var LessonAvailability
     */
    private $availa;

    protected function setUp()
    {
        parent::setUp();

        $this->availa = new LessonAvailability($this->data);
    }

    /** @test */
    public function it_shows_every_possible_availability()
    {
        $this->assertEquals(new LessonAvailability([
            1 => [1, 2], // monday
            2 => [1, 2], // tuesday
            3 => [1, 2], // wednesday
            4 => [1, 2], // thursday
            5 => [1, 2], // friday
        ]), LessonAvailability::all());
    }

    /** @test */
    public function it_marks_availability_in_time_slots_for_weekdays()
    {
        $availability = new LessonAvailability([2 => [1, 2], 4 => [2]]);

        $this->assertEquals([1, 2], $availability->availabilityFor(2));
    }

    /** @test */
    public function it_checks_for_a_specific_day_and_slot()
    {
        $availability = new LessonAvailability([2 => [1, 2], 4 => [2]]);

        $this->assertTrue($availability->isAvailable(2, 2));
        $this->assertTrue($availability->isAvailable(4, 2));
        $this->assertFalse($availability->isAvailable(4, 1));
        $this->assertFalse($availability->isAvailable(3, 1));
    }

    /** @test */
    public function it_also_gets_slots_from_array_keys()
    {
        // Getting from key is easier to the front-end
        $availability = new LessonAvailability([2 => [1 => 'ok'], 3 => [1 => 2]]);

        $this->assertTrue($availability->isAvailable(2, 1));
        $this->assertTrue($availability->isAvailable(3, 2)); // from value
        $this->assertTrue($availability->isAvailable(3, 1)); // from key
    }

    /** @test */
    public function it_has_observations()
    {
        $availability = new LessonAvailability(['obs' => 'my obs']);

        $this->assertEquals('my obs', $availability->observations());
    }

    /** @test */
    public function it_counts_the_number_of_days_available()
    {
        $this->assertEquals(2, $this->availa->daysAvailable());
    }

    /** @test */
    public function it_is_arrayable()
    {
        $this->assertEquals($this->data, $this->availa->toArray());
    }

    /** @test */
    public function it_can_be_null()
    {
        $availability = new LessonAvailability(null);

        $this->assertEquals(0, $availability->daysAvailable());
    }
}
