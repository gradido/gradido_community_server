<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\StateBalancesController Test Case
 *
 * @uses \App\Controller\StateBalancesController
 */
class StateBalancesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.TransactionCreations',
        'app.Transactions',
        'app.StateUsers',
        'app.StateErrors',
        'app.TransactionSignatures',
        'app.TransactionSendCoins',
        'app.StateBalances',
        'app.TransactionTypes'
    ];

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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test overview method
     *
     * @return void
     */
    public function testOverview()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test ajaxGetBalance method
     *
     * @return void
     */
    public function testAjaxGetBalance()
    {
        $session_id = rand();
        $balance = rand();
        $this->session([
            'session_id' => $session_id,
            'Transaction' => ['pending' => 0, 'executing' => 0],
            'StateUser' => [
                'id' => 2, // 1 don't work, I don't know why
                'email_checked' => 1,
                'balance' => $balance
            ]
        ]);
        //echo "balance: $balance";
        $this->getAndParse('/state-balances/ajaxGetBalance/' . $session_id, 
                ['state' => 'success', 'balance' => $balance]
        );
    }

    /**
     * Test ajaxListTransactions method
     *
     * @return void
     */
    public function testAjaxListTransactions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test overviewGdt method
     *
     * @return void
     */
    public function testOverviewGdt()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test sortTransactions method
     *
     * @return void
     */
    public function testSortTransactions()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    
    
    private function getAndParse($path, $expected)
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        
        $this->disableErrorHandlerMiddleware();
        $this->get($path);        
        
        // Check that the response was a 200
        $this->assertResponseOk();     
        
        $responseBodyString = (string)$this->_response->getBody();
        $json = json_decode($responseBodyString);
        $this->assertNotFalse($json);
        
        if(is_array($expected)) {
          $expected = json_encode($expected);
        }
        $this->assertEquals($expected, $responseBodyString);
    }
}
