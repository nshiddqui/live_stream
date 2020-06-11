<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PasswordRecordsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PasswordRecordsTable Test Case
 */
class PasswordRecordsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PasswordRecordsTable
     */
    public $PasswordRecords;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PasswordRecords',
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
        $config = TableRegistry::getTableLocator()->exists('PasswordRecords') ? [] : ['className' => PasswordRecordsTable::class];
        $this->PasswordRecords = TableRegistry::getTableLocator()->get('PasswordRecords', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PasswordRecords);

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
