<?php

namespace Tests\Unit;

use App\Model\ModelFilter;

use Illuminate\Http\Request;
use Tests\TestCase;

class ModelFilterTest extends TestCase
{
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = (object)[]; // dummy builder
    }

    /** @test */
    public function a_filter_is_called_when_present_in_the_request()
    {
        $request = new Request(['trending-topics' => 10]);
        $filters = new TestModelFilter($request);

        $filters->apply($this->builder);

        $this->assertTrue($this->builder->called);
    }

    /** @test */
    public function a_default_filter_can_be_defined_if_no_filters_where_found()
    {
        $request = new Request(['not-defined-filter']);
        $filters = new TestModelFilter($request);

        $called = false;
        $filters->apply($this->builder, function() use (&$called) {
            $called = true;
        });
        $this->assertTrue($called);

        // Sanity check
        $request = new Request(['trending-topics' => 10]);
        $filters = new TestModelFilter($request);

        $called = false;
        $filters->apply($this->builder, function() use (&$called) {
            $called = true;
        });
        $this->assertFalse($called);
    }

}

class TestModelFilter extends ModelFilter
{
    protected $filters = ['trending-topics'];

    public function trendingTopics()
    {
        $this->builder->called = true;
    }
}