<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StreamDetailsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StreamDetailsTable Test Case
 */
class StreamDetailsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StreamDetailsTable
     */
    public $StreamDetails;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.StreamDetails',
        'app.Streams',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('StreamDetails') ? [] : ['className' => StreamDetailsTable::class];
        $this->StreamDetails = TableRegistry::getTableLocator()->get('StreamDetails', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->StreamDetails);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
