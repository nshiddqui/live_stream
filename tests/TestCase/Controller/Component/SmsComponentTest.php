<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\SmsComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\SmsComponent Test Case
 */
class SmsComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\SmsComponent
     */
    public $Sms;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Sms = new SmsComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Sms);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
