<?php

namespace Tests\Unit;

use App\Model\Entity;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EntityTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function it_ensures_private_static_methods_are_not_public()
    {
        TestEntity::doSomethingPrivate();
    }

    /** @test */
    public function it_allows_private_static_methods_to_be_private()
    {
        TestEntity::$calledPrivate = false;

        TestEntity::callPrivate();

        $this->assertTrue(TestEntity::$calledPrivate);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException */
    public function it_ensures_protected_static_methods_are_not_public()
    {
        DerivedTestEntity::doSomethingProtected();
    }

    /** @test */
    public function it_allows_protected_static_methods_to_be_protected()
    {
        DerivedTestEntity::$calledProtected = false;

        DerivedTestEntity::callProtected();

        $this->assertTrue(DerivedTestEntity::$calledProtected);
    }
}

class TestEntity extends Entity
{
    public static $calledProtected = false;
    public static $calledPrivate = false;

    protected static function doSomethingProtected() {
        static::$calledProtected = true;
    }

    public static function callPrivate() {
        self::doSomethingPrivate();
    }

    private static function doSomethingPrivate() {
        static::$calledPrivate = true;
    }
}

class DerivedTestEntity extends TestEntity
{
    public static function callProtected() {
        self::doSomethingProtected();
    }
}