<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use App\Controller\AppController;

use Model\Navigation\NaviHierarchy;
use Model\Navigation\NaviHierarchyEntry;

/**
 * StateBalances Controller
 *
 * @property \App\Model\Table\StateBalancesTable $StateBalances
 *
 * @method \App\Model\Entity\StateBalance[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StateBalancesController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        //$this->Auth->allow(['add', 'edit']);
        $this->Auth->allow(['overview', 'overviewGdt', 'ajaxGetBalance']);
        $this->loadComponent('JsonRequestClient');
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['StateUsers']
        ];
        $stateBalances = $this->paginate($this->StateBalances);

        $this->set(compact('stateBalances'));
    }
  
    

    public function overview()
    {
        $this->set(
            'naviHierarchy',
            (new NaviHierarchy())->
            add(new NaviHierarchyEntry(__('Startseite'), 'Dashboard', 'index', false))->
            add(new NaviHierarchyEntry(__('Kontoübersicht'), 'StateBalances', 'overview', true))
        );
        $startTime = microtime(true);
        $this->viewBuilder()->setLayout('frontend');
        $session = $this->getRequest()->getSession();
        $result = $this->requestLogin();
        if ($result !== true) {
            return $result;
        }
        $user = $session->read('StateUser');
        // sendRequestGDT
        // listPerEmailApi

        $gdtSum = 0;
        //if('admin' === $user['role']) {
          $gdtEntries = $this->JsonRequestClient->sendRequestGDT(['email' => $user['email']], 'GdtEntries' . DS . 'sumPerEmailApi');
          //var_dump($gdtEntries);
          if('success' == $gdtEntries['state'] && 'success' == $gdtEntries['data']['state']) {
            $gdtSum = intval($gdtEntries['data']['sum']);
          } else {
            if($user) {
              $this->addAdminError('StateBalancesController', 'overview', $gdtEntries, $user['id']);
            } else {
              $this->addAdminError('StateBalancesController', 'overview', $gdtEntries, 0);
            }
          }
        //}
        //
        //

        $creationsTable = TableRegistry::getTableLocator()->get('TransactionCreations');
        $creationTransactions = $creationsTable
                ->find('all')
                ->where(['state_user_id' => $user['id']])
                ->contain(['Transactions']);

        $transferTable = TableRegistry::getTableLocator()->get('TransactionSendCoins');
        $transferTransactions = $transferTable
                ->find('all')
                ->where(['OR' => ['state_user_id' => $user['id'], 'receiver_user_id' => $user['id']]])
                ->contain(['Transactions']);

        $involvedUserIds = [];

        foreach ($transferTransactions as $sendCoins) {
          //var_dump($sendCoins);
            if ($sendCoins->state_user_id != $user['id']) {
                array_push($involvedUserIds, intval($sendCoins->state_user_id));
            } elseif ($sendCoins->receiver_user_id != $user['id']) {
                array_push($involvedUserIds, intval($sendCoins->receiver_user_id));
            }
        }

        /*echo "state user from sendCoins: $sendCoins->state_user_id<br>";
        echo "receiver user from sendCoins: $sendCoins->receiver_user_id<br>";
        echo "user id from logged in user: ".$user['id']. '<br>';
        */
        //var_dump($involvedUserIds);
        // exchange key with values and drop duplicates
        $involvedUser_temp = array_flip($involvedUserIds);
        // exchange back
        $involvedUserIds = array_flip($involvedUser_temp);
        $userTable = TableRegistry::getTableLocator()->get('StateUsers');
        $involvedUser = $userTable->find('all', [
            'contain' => false,
            'where' => ['id IN' => $involvedUserIds],
            'fields' => ['id', 'first_name', 'last_name', 'email']
          ]);
        //var_dump($involvedUser->toArray());
        $involvedUserIndices = [];
        foreach ($involvedUser as $involvedUser) {
            $involvedUserIndices[$involvedUser->id] = $involvedUser;
        }

        // sender or receiver when user has sended money
        // group name if creation
        // type: gesendet / empfangen / geschöpft
        // transaktion nr / id
        // date
        // balance

        $transactions = [];
        foreach ($creationTransactions as $creation) {
          //var_dump($creation);
            array_push($transactions, [
              'name' => 'Gradido Akademie',
              'type' => 'creation',
              'transaction_id' => $creation->transaction_id,
              'date' => $creation->transaction->received,
              'balance' => $creation->amount,
              'memo' => $creation->transaction->memo
            ]);
        }

        foreach ($transferTransactions as $sendCoins) {
            $type = '';
            $otherUser = null;
            if ($sendCoins->state_user_id == $user['id']) {
                $type = 'send';
                
                if(isset($involvedUserIndices[$sendCoins->receiver_user_id])) {
                  $otherUser = $involvedUserIndices[$sendCoins->receiver_user_id];
                }
            } else if ($sendCoins->receiver_user_id == $user['id']) {
                $type = 'receive';
                if(isset($involvedUserIndices[$sendCoins->state_user_id])) {
                  $otherUser = $involvedUserIndices[$sendCoins->state_user_id];
                }
            }
            if(null == $otherUser) {
              $otherUser = $this->StateBalances->StateUsers->newEntity();
            }
            array_push($transactions, [
             'name' => $otherUser->first_name . ' ' . $otherUser->last_name,
             'email' => $otherUser->email,
             'type' => $type,
             'transaction_id' => $sendCoins->transaction_id,
             'date' => $sendCoins->transaction->received,
             'balance' => $sendCoins->amount,
             'memo' => $sendCoins->transaction->memo
            ]);
        }
        uasort($transactions, array($this, 'sortTransactions'));
        $this->set('transactions', $transactions);
        $this->set('transactionExecutingCount', $session->read('Transaction.executing'));
        $this->set('balance', $session->read('StateUser.balance'));
        $this->set('timeUsed', microtime(true) - $startTime);
        $this->set('gdtSum', $gdtSum);
    }
    
    public function ajaxGetBalance($session_id)
    {
        if(!isset($session_id) || !$session_id) {
            return $this->returnJson(['state' => 'error', 'msg' => 'invalid session']);
        }
        $startTime = microtime(true);
        $session = $this->getRequest()->getSession();
        $result = $this->requestLogin($session_id);
        if ($result !== true) {
            return $this->returnJson(['state' => 'error', 'msg' => 'session not found']);
        }
        $user = $session->read('StateUser');
        //var_dump($user);
        return $this->returnJson(['state' => 'success', 'balance' => $user['balance']]);
        
    }
    
    public function ajaxListTransactions($session_id, $page, $count)
    {
        if(!isset($session_id) || !$session_id) {
            return $this->returnJson(['state' => 'error', 'msg' => 'invalid session']);
        }
        $startTime = microtime(true);
        $session = $this->getRequest()->getSession();
        $result = $this->requestLogin($session_id);
        if ($result !== true) {
            return $this->returnJson(['state' => 'error', 'msg' => 'session not found']);
        }
        $user = $session->read('StateUser');
        
        $gdtSum = 0;
        
        $gdtEntries = $this->JsonRequestClient->sendRequestGDT(['email' => $user['email']], 'GdtEntries' . DS . 'sumPerEmailApi');

        if('success' == $gdtEntries['state'] && 'success' == $gdtEntries['data']['state']) {
          $gdtSum = intval($gdtEntries['data']['sum']);
        } else {
          if($user) {
              
            $this->addAdminError('StateBalancesController', 'overview', $gdtEntries, $user['id']);
          } else {
            $this->addAdminError('StateBalancesController', 'overview', $gdtEntries, 0);
          }
        }
        

        $creationsTable = TableRegistry::getTableLocator()->get('TransactionCreations');
        $creationTransactions = $creationsTable
                ->find('all')
                ->where(['state_user_id' => $user['id']])
                ->contain(['Transactions']);

        $transferTable = TableRegistry::getTableLocator()->get('TransactionSendCoins');
        $transferTransactions = $transferTable
                ->find('all')
                ->where(['OR' => ['state_user_id' => $user['id'], 'receiver_user_id' => $user['id']]])
                ->contain(['Transactions']);

        $involvedUserIds = [];

        foreach ($transferTransactions as $sendCoins) {
          //var_dump($sendCoins);
            if ($sendCoins->state_user_id != $user['id']) {
                array_push($involvedUserIds, intval($sendCoins->state_user_id));
            } elseif ($sendCoins->receiver_user_id != $user['id']) {
                array_push($involvedUserIds, intval($sendCoins->receiver_user_id));
            }
        }

        /*echo "state user from sendCoins: $sendCoins->state_user_id<br>";
        echo "receiver user from sendCoins: $sendCoins->receiver_user_id<br>";
        echo "user id from logged in user: ".$user['id']. '<br>';
        */
        //var_dump($involvedUserIds);
        // exchange key with values and drop duplicates
        $involvedUser_temp = array_flip($involvedUserIds);
        // exchange back
        $involvedUserIds = array_flip($involvedUser_temp);
        $userTable = TableRegistry::getTableLocator()->get('StateUsers');
        $involvedUser = $userTable->find('all', [
            'contain' => false,
            'where' => ['id IN' => $involvedUserIds],
            'fields' => ['id', 'first_name', 'last_name', 'email']
          ]);
        //var_dump($involvedUser->toArray());
        $involvedUserIndices = [];
        foreach ($involvedUser as $involvedUser) {
            $involvedUserIndices[$involvedUser->id] = $involvedUser;
        }

        // sender or receiver when user has sended money
        // group name if creation
        // type: gesendet / empfangen / geschöpft
        // transaktion nr / id
        // date
        // balance

        $transactions = [];
        foreach ($creationTransactions as $creation) {
          //var_dump($creation);
            array_push($transactions, [
              'name' => 'Gradido Akademie',
              'type' => 'creation',
              'transaction_id' => $creation->transaction_id,
              'date' => $creation->transaction->received,
              'balance' => $creation->amount,
              'memo' => $creation->transaction->memo
            ]);
        }

        foreach ($transferTransactions as $sendCoins) {
            $type = '';
            $otherUser = null;
            if ($sendCoins->state_user_id == $user['id']) {
                $type = 'send';
                
                if(isset($involvedUserIndices[$sendCoins->receiver_user_id])) {
                  $otherUser = $involvedUserIndices[$sendCoins->receiver_user_id];
                }
            } else if ($sendCoins->receiver_user_id == $user['id']) {
                $type = 'receive';
                if(isset($involvedUserIndices[$sendCoins->state_user_id])) {
                  $otherUser = $involvedUserIndices[$sendCoins->state_user_id];
                }
            }
            if(null == $otherUser) {
              $otherUser = $this->StateBalances->StateUsers->newEntity();
            }
            array_push($transactions, [
             'name' => $otherUser->first_name . ' ' . $otherUser->last_name,
             'email' => $otherUser->email,
             'type' => $type,
             'transaction_id' => $sendCoins->transaction_id,
             'date' => $sendCoins->transaction->received,
             'balance' => $sendCoins->amount,
             'memo' => $sendCoins->transaction->memo
            ]);
        }
        uasort($transactions, array($this, 'sortTransactions'));
        return $this->returnJson([
                'state' => 'success',
                'transactions' => $transactions,
                'transactionExecutingCount' => $session->read('Transaction.executing'),
                'count' => count($transactions),
                'gdtSum' => $gdtSum,
                'timeUsed' => microtime(true) - $startTime
            ]);
    }

    public function overviewGdt()
    {
        $this->set(
            'naviHierarchy',
            (new NaviHierarchy())->
            add(new NaviHierarchyEntry(__('Startseite'), 'Dashboard', 'index', false))->
            add(new NaviHierarchyEntry(__('GDT Kontoübersicht'), 'StateBalances', 'overviewGdt', true))
        );
        $startTime = microtime(true);
        $this->viewBuilder()->setLayout('frontend');
        $session = $this->getRequest()->getSession();
        $result = $this->requestLogin();
        if ($result !== true) {
            return $result;
        }
        $user = $session->read('StateUser');
        $requestResult = $this->JsonRequestClient->sendRequestGDT(['email' => $user['email']], 'GdtEntries' . DS . 'listPerEmailApi');

        //var_dump($requestResult);
        if('success' === $requestResult['state'] && 'success' === $requestResult['data']['state']) {

          //var_dump(array_keys($requestResult['data']));
            $ownEntries = $requestResult['data']['ownEntries'];
          //$gdtEntries = $requestResult['data']['entries'];

            $gdtSum = 0;
            foreach ($ownEntries as $i => $gdtEntry) {
                $gdtSum += $gdtEntry['gdt'];
              //echo "index: $i<br>";
              //var_dump($gdtEntry);
            }
            if (isset($requestResult['data']['connectEntrys'])) {
                $connectEntries = $requestResult['data']['connectEntrys'];

                foreach ($connectEntries as $entry) {
                  //if(!$count) var_dump($entry);
                  //$count++;
                    $gdtSum += $entry['connect']['gdt_entry']['gdt'];
                }
                $this->set('connectEntries', $connectEntries);
            }

          //echo "gdtSum: $gdtSum<br>";
            $this->set('gdtSum', $gdtSum);
            $this->set('ownEntries', $ownEntries);
			$this->set('gdtSumPerEmail', $requestResult['data']['gdtSumPerEmail']);
			$this->set('moreEntrysAsShown', $requestResult['data']['moreEntrysAsShown']);
			$this->set('user', $user);

            if (isset($requestResult['data']['publishers'])) {
                $publishers = $requestResult['data']['publishers'];
                $this->set('publishers', $publishers);
            }
        } else {
          $this->addAdminError('StateBalancesController', 'overviewGdt', $requestResult, $user['id']);
          $this->Flash->error(__('Fehler beim GDT Server, bitte abwarten oder den Admin benachrichtigen!'));
        }
    }

    public function sortTransactions($a, $b)
    {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] > $b['date']) ? -1 : 1;
    }

    /**
     * View method
     *
     * @param string|null $id State Balance id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $stateBalance = $this->StateBalances->get($id, [
            'contain' => ['StateUsers']
        ]);

        $this->set('stateBalance', $stateBalance);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stateBalance = $this->StateBalances->newEntity();
        if ($this->request->is('post')) {
            $stateBalance = $this->StateBalances->patchEntity($stateBalance, $this->request->getData());
            if ($this->StateBalances->save($stateBalance)) {
                $this->Flash->success(__('The state balance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The state balance could not be saved. Please, try again.'));
        }
        $stateUsers = $this->StateBalances->StateUsers->find('list', ['limit' => 200]);
        $this->set(compact('stateBalance', 'stateUsers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id State Balance id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $stateBalance = $this->StateBalances->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stateBalance = $this->StateBalances->patchEntity($stateBalance, $this->request->getData());
            if ($this->StateBalances->save($stateBalance)) {
                $this->Flash->success(__('The state balance has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The state balance could not be saved. Please, try again.'));
        }
        $stateUsers = $this->StateBalances->StateUsers->find('list', ['limit' => 200]);
        $this->set(compact('stateBalance', 'stateUsers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id State Balance id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stateBalance = $this->StateBalances->get($id);
        if ($this->StateBalances->delete($stateBalance)) {
            $this->Flash->success(__('The state balance has been deleted.'));
        } else {
            $this->Flash->error(__('The state balance could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
