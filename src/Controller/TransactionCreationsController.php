<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
//use Cake\I18n\Number;
use Cake\Http\Client;
use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use Cake\Datasource\ConnectionManager;

use Model\Navigation\NaviHierarchy;
use Model\Navigation\NaviHierarchyEntry;

use App\Form\CreationForm;
// protobuf transactions
//use Model\Messages\Gradido\TransactionCreation;
use Model\Transactions\TransactionCreation;

/**
 * TransactionCreations Controller
 *
 * @property \App\Model\Table\TransactionCreationsTable $TransactionCreations
 *
 * @method \App\Model\Entity\TransactionCreation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransactionCreationsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('GradidoNumber');
        $this->loadComponent('JsonRequestClient');
        //$this->Auth->allow(['add', 'edit']);
        //$this->Auth->allow('create');
        $this->set(
            'naviHierarchy',
            (new NaviHierarchy())->
            add(new NaviHierarchyEntry(__('Startseite'), 'Dashboard', 'index', false))->add(new NaviHierarchyEntry(__('Gradido schöpfen'), 'TransactionCreations', 'create-multi', true))
        );
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Transactions', 'StateUsers']
        ];
        $transactionCreations = $this->paginate($this->TransactionCreations);
        $identHashes = [];
        foreach ($transactionCreations as $creation) {
            $identHash = TransactionCreation::DRMakeStringHash($creation->state_user->email);
            $identHashes[$creation->state_user->id] = $identHash;
        }

        $this->set(compact('transactionCreations', 'identHashes'));
    }

    /**
     * View method
     *
     * @param string|null $id Transaction Creation id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transactionCreation = $this->TransactionCreations->get($id, [
            'contain' => ['Transactions', 'StateUsers']
        ]);

        $this->set('transactionCreation', $transactionCreation);
    }

    public function create()
    {
        $startTime = microtime(true);
        $this->viewBuilder()->setLayout('frontend');
        $session = $this->getRequest()->getSession();
        $user = $session->read('StateUser');
        if (!$user) {
            $result = $this->requestLogin();
            if ($result !== true) {
                return $result;
            }
            $user = $session->read('StateUser');
        }
        $creationForm = new CreationForm();
        $transactionCreation = $this->TransactionCreations->newEntity();
        $transactionCreation->state_user_id  = $user['id'];

        // adding possible addresses + input field for copy
        $stateUserTable = TableRegistry::getTableLocator()->get('StateUsers');
        $stateUsers = $stateUserTable->find('all')->contain(false);
        $receiverProposal = [];
        foreach ($stateUsers as $stateUser) {
            $name = $stateUser->email;
            $keyHex = bin2hex(stream_get_contents($stateUser->public_key));
            if ($name === null) {
                $name = $stateUser->first_name . ' ' . $stateUser->last_name;
            }
            array_push($receiverProposal, ['name' => $name, 'key' => $keyHex, 'email' => $stateUser->email]);
          //$stateUser->public_key
        }
        $timeUsed = microtime(true) - $startTime;
        $this->set(compact('transactionCreation', 'timeUsed', 'receiverProposal', 'creationForm'));

        if ($this->request->is('post')) {
            $requestData = $this->request->getData();
            $mode = 'next';
            if (isset($requestData['add'])) {
                $mode = 'add';
            }
            if ($creationForm->validate($requestData)) {
                $pubKeyHex = '';
                $identHash = '';
                $amountCent = $this->GradidoNumber->parseInputNumberToCentNumber($requestData['amount']);
                $receiverIndex = intval($requestData['receiver'])-1;

                if (count($receiverProposal) > $receiverIndex) {
                    $pubKeyHex = $receiverProposal[$receiverIndex]['key'];
                    $identHash = TransactionCreation::DRMakeStringHash($receiverProposal[$receiverIndex]['email']);
                }
                $builderResult = TransactionCreation::build(
                    $amountCent,
                    $requestData['memo'],
                    $pubKeyHex,
                    $identHash
                );
                if ($builderResult['state'] == 'success') {
                    $user_balance = 0;
                    if (isset($user['balance'])) {
                        $user_balance = $user['balance'];
                    }
                    // $session_id, $base64Message, $user_balance = 0
                    $requestResult = $this->JsonRequestClient->sendTransaction(
                        $session->read('session_id'),
                        base64_encode($builderResult['transactionBody']->serializeToString()),
                        $user_balance
                    );
                    if ($requestResult['state'] != 'success') {
                          $this->addAdminError('TransactionCreations', 'create', $requestResult, $user['id']);
                        if ($requestResult['type'] == 'request error') {
                            $this->Flash->error(__('Error by requesting LoginServer, please try again'));
                        } else {
                            $this->Flash->error(__('Error, please wait for the admin to fix it'));
                        }
                    } else {
                        $json = $requestResult['data'];
                        if ($json['state'] != 'success') {
                            if ($json['msg'] == 'session not found') {
                                      $session->destroy();
                                      return $this->redirect($this->loginServerUrl . 'account', 303);
                            } else {
                                    $this->addAdminError('TransactionCreations', 'create', $json, $user['id']);
                                    $this->Flash->error(__('Login Server Error, please wait for the admin to fix it'));
                            }
                        } else {
                            $pendingTransactionCount = $session->read('Transactions.pending');
                            if ($pendingTransactionCount == null) {
                                $pendingTransactionCount = 1;
                            } else {
                                $pendingTransactionCount++;
                            }
                            $session->write('Transactions.pending', $pendingTransactionCount);
                            if ($mode === 'next') {
                                return $this->redirect($this->loginServerUrl . 'account/checkTransactions', 303);
                            } else {
                                $this->Flash->success(__('Transaction submitted for review.'));
                            }
                        }
                    }
                } else {
                    $this->Flash->error(__('Building transaction failed'));
                }
  //           */
            } else {
                $this->Flash->error(__('Something was invalid, please try again!'));
            }
        }
    }

    public function createMulti($page = 0)
    {
        $startTime = microtime(true);
        $this->viewBuilder()->setLayout('frontend');
        $session = $this->getRequest()->getSession();
        $result = $this->requestLogin();
        $limit = 200;
        if ($result !== true) {
            return $result;
        }
        $user = $session->read('StateUser');

        $stateUserTable = TableRegistry::getTableLocator()->get('StateUsers');

        $connection = ConnectionManager::get('default');
        $transactionActiveMonth = $connection->execute(
            'SELECT id, received FROM transactions '
                . 'where received >= date_sub(date_add(curdate(), interval 1 - day(curdate()) day), interval 2 month) '
                . 'AND '
                . 'received < date_add(date_add(curdate(), interval 1 - day(curdate()) day), interval 2 month) '
                . 'AND '
                . 'transaction_type_id = 1'
        )->fetchAll('assoc');
        $transactionActiveMonthSortedById = [];
        foreach ($transactionActiveMonth as $t) {
            $transactionActiveMonthSortedById[$t['id']] = $t['received'];
        }
        $firstDayLastMonth = new FrozenDate();
        $firstDayLastMonth = $firstDayLastMonth->day(1)->subMonth(1);
        $transactionsLastMonthTargeDate = $this->TransactionCreations
                ->find('all')
                //->select(['state_user_id', 'target_date', 'amount'])
                ->where(['EXTRACT(YEAR_MONTH FROM target_date) LIKE' => $firstDayLastMonth->format('Ym')])
                ->group(['state_user_id'])
                ->contain([]);
        $transactionsLastMonthTargeDate->select([
            'state_user_id',
            'sum_amount' => $transactionsLastMonthTargeDate->func()->sum('amount')
        ]);

        $transactionsLastMonthTargetDateSortedByStateUserId = [];
        foreach ($transactionsLastMonthTargeDate as $transactionCreation) {
            $transactionsLastMonthTargetDateSortedByStateUserId[$transactionCreation->state_user_id] = $transactionCreation->sum_amount;
        }

        $requestData = $this->request->getData();
        if ($this->request->is('post') &&
          isset($requestData['searchButton']) &&
          isset($requestData['searchText']) &&
          !empty($requestData['searchText'])
        ) {
            $mode = 'search';
            $page = 0;
            $this->log("search for text: ".$requestData['searchText'], 'debug');
            $stateUsers = $stateUserTable
                ->find('all')
                ->select(['id', 'first_name', 'last_name', 'email'])
                ->order(['first_name', 'last_name'])
                ->where(
                    ['AND' => [
                        'disabled' => 0,
                            'OR' => [
                                      'LOWER(first_name) LIKE' => '%'.strtolower($requestData['searchText']).'%',
                                      'LOWER(last_name) LIKE' => '%'.strtolower($requestData['searchText']).'%',
                                      'LOWER(email) LIKE' => '%'.strtolower($requestData['searchText']).'%'
                                    ]
                                ]
                            ]
                )
                ->contain(['TransactionCreations' => [
                    'fields' => [
                        'TransactionCreations.amount',
                        'TransactionCreations.transaction_id',
                        'TransactionCreations.state_user_id'
                    ]
                ]]);
            $this->log("search query: ".$stateUsers, 'debug');
        } else {
            $stateUsers = $stateUserTable
                ->find('all')
                ->select(['id', 'first_name', 'last_name', 'email'])
                //->order(['id'])
				->where(['disabled' => 0])
                ->order(['first_name', 'last_name'])
                ->contain(['TransactionCreations' => [
                    'fields' => [
                        'TransactionCreations.amount',
                        'TransactionCreations.transaction_id',
                        'TransactionCreations.state_user_id'
                    ]
                ]]);
        }

        //var_dump($stateUsers->toArray());
        $possibleReceivers = [];
        $countUsers = 0;
        foreach ($stateUsers as $i => $stateUser) {
            $countUsers++;
            if ($i < $page * $limit || $i >= ($page + 1) * $limit) {
                continue;
            }
            $sumAmount = 0;
            $sumAmount2 = 0;
            if (isset($transactionsLastMonthTargetDateSortedByStateUserId[$stateUser->id])) {
                $sumAmount2 = $transactionsLastMonthTargetDateSortedByStateUserId[$stateUser->id];
            }
            foreach ($stateUser->transaction_creations as $transactionCreation) {
              //var_dump($transactionCreation);
                if (isset($transactionActiveMonthSortedById[$transactionCreation->transaction_id])) {
                    $sumAmount += $transactionCreation->amount;
                }
            }

          //if($sumAmount < 20000000) {
            array_push($possibleReceivers, [
                'name' => $stateUser->first_name . '&nbsp;' . $stateUser->last_name,
                'id' => $stateUser->id,
                'email' => $stateUser->email,
                'amount' => $sumAmount,
                'amount2' => $sumAmount2
                ]);
          /*} else {
            $this->Flash->error(__('Creation above 2.000 GDD for 2 last two month'));
          }*/
        }
        // usort($possibleReceivers, function ($a, $b) {
        //     return (strtolower($a['name']) <=> strtolower($b['name']));
        // });
        // -> replaced by SQL "order by" above
        $creationForm = new CreationForm();

        $timeUsed = microtime(true) - $startTime;
        $this->set(compact('timeUsed', 'stateUsers', 'creationForm', 'possibleReceivers'));

        $this->set('firstDayLastMonth', $firstDayLastMonth);
        $this->set('activeUser', $user);
        $this->set('creationForm', $creationForm);
        $this->set('transactionExecutingCount', $session->read('Transaction.executing'));
        $this->set('timeUsed', microtime(true) - $startTime);
        $this->set('countUsers', $countUsers);
        $this->set('limit', $limit);
        $this->set('page', $page);

        if ($this->request->is('post') && (!isset($mode) || !($mode === 'search'))) {
            $this->log("real POST", 'debug');
            $mode = 'next';
            if (isset($requestData['add'])) {
                $mode = 'add';
            }
            $memo = $requestData['memo'];
            $amountCent = $this->GradidoNumber->parseInputNumberToCentNumber($requestData['amount']);
          //$targetDate = $requestData['target_date'];
            if (!isset($requestData['user']) || count($requestData['user']) == 0) {
                $this->Flash->error(__('No user selected'));
            } else {
                $users = $requestData['user'];
                if (isset($requestData['user_pending'])) {
                    $pendings = $requestData['user_pending'];
                } else {
                    $pendings = [];
                }
                $receiverUsers = $stateUserTable
                ->find('all')
                ->where(['id IN' => array_keys($users)])
                ->select(['public_key', 'email', 'id'])
                ->contain(false);
                $transactions  = [];
                foreach ($receiverUsers as $receiverUser) {
                    $localAmountCent = $amountCent;
                    //$localTargetDate = $targetDate;
                    $id = $receiverUser->id;
                    if ($requestData['user_amount'][$id] != '') {
                        $localAmountCent = $this->GradidoNumber->parseInputNumberToCentNumber($requestData['user_amount'][$id]);
                    }
                    if (isset($requestData['user_target_date']) && isset($requestData['user_target_date'][$id])) {
                        $localTargetDate = $requestData['user_target_date'][$id];
                    }
                    if (isset($pendings[$id])) {
                        $pendings[$id] += $localAmountCent;
                    } else {
                        $pendings[$id] = $localAmountCent;
                    }
                    $pubKeyHex = bin2hex(stream_get_contents($receiverUser->public_key));
                    $identHash = TransactionCreation::DRMakeStringHash($receiverUser->email);
                    $localTargetDateFrozen = FrozenDate::now();
                    $localTargetDateFrozen = $localTargetDateFrozen
                    ->year($localTargetDate['year'])
                    ->month($localTargetDate['month'])
                    ->day($localTargetDate['day']);
                    //echo "input: "; var_dump($localTargetDate);echo "<br>";
                    //echo "output: "; var_dump($localTargetDateFrozen);
                    //die('a');
                    $builderResult = TransactionCreation::build(
                        $localAmountCent,
                        $memo,
                        $pubKeyHex,
                        $identHash,
                        $localTargetDateFrozen
                    );
                    if ($builderResult['state'] == 'success') {
                          array_push($transactions, base64_encode($builderResult['transactionBody']->serializeToString()));
                    }
                }
                /*echo "pendings: ";
                var_dump($pendings);
                echo "<br>";*/
                foreach ($possibleReceivers as $i => $possibleReceiver) {
                    $id = $possibleReceiver['id'];
                    if (isset($pendings[$id])) {
                        $possibleReceivers[$i]['pending'] = $pendings[$id];
                    }
                }
                $this->set('possibleReceivers', $possibleReceivers);
                $creationTransactionCount = count($transactions);
                if ($creationTransactionCount > 0) {
                    $user_balance = 0;
                    if (isset($user['balance'])) {
                        $user_balance = $user['balance'];
                    }
                    // $session_id, $base64Message, $user_balance = 0
                    $requestResult = $this->JsonRequestClient->sendTransaction(
                        $session->read('session_id'),
                        $transactions,
                        $user_balance
                    );
                    if ($requestResult['state'] != 'success') {
                        $this->addAdminError('TransactionCreations', 'createMulti', $requestResult, $user['id']);
                        if ($requestResult['type'] == 'request error') {
                            $this->Flash->error(__('Error by requesting LoginServer, please try again'));
                        } else {
                            $this->Flash->error(__('Error, please wait for the admin to fix it'));
                        }
                    } else {
                        $json = $requestResult['data'];
                        if ($json['state'] != 'success') {
                            if ($json['msg'] == 'session not found') {
                                $session->destroy();
                                return $this->redirect($this->loginServerUrl . 'account', 303);
                            } else {
                                $this->addAdminError('TransactionCreations', 'createMulti', $json, $user['id']);
                                $this->Flash->error(__('Login Server Error, please wait for the admin to fix it'));
                            }
                        } else {
                            $pendingTransactionCount = $session->read('Transactions.pending');
                            if ($pendingTransactionCount == null) {
                                $pendingTransactionCount = $creationTransactionCount;
                            } else {
                                $pendingTransactionCount += $creationTransactionCount;
                            }
                            $session->write('Transactions.pending', $pendingTransactionCount);
                            if ($mode === 'next') {
                                return $this->redirect($this->loginServerUrl . 'account/checkTransactions', 303);
                            } else {
                                $this->Flash->success(__('Transaction submitted for review.'));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        $transactionCreation = $this->TransactionCreations->newEntity();
        if ($this->request->is('post')) {
            $transactionCreation = $this->TransactionCreations->patchEntity($transactionCreation, $this->request->getData());
            if ($this->TransactionCreations->save($transactionCreation)) {
                $this->Flash->success(__('The transaction creation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction creation could not be saved. Please, try again.'));
        }
        $transactions = $this->TransactionCreations->Transactions->find('list', ['limit' => 200]);
        $stateUsers = $this->TransactionCreations->StateUsers->find('list', ['limit' => 200]);
        $this->set(compact('transactionCreation', 'transactions', 'stateUsers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Transaction Creation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $transactionCreation = $this->TransactionCreations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transactionCreation = $this->TransactionCreations->patchEntity($transactionCreation, $this->request->getData());
            if ($this->TransactionCreations->save($transactionCreation)) {
                $this->Flash->success(__('The transaction creation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction creation could not be saved. Please, try again.'));
        }
        $transactions = $this->TransactionCreations->Transactions->find('list', ['limit' => 200]);
        $stateUsers = $this->TransactionCreations->StateUsers->find('list', ['limit' => 200]);
        $this->set(compact('transactionCreation', 'transactions', 'stateUsers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction Creation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transactionCreation = $this->TransactionCreations->get($id);
        if ($this->TransactionCreations->delete($transactionCreation)) {
            $this->Flash->success(__('The transaction creation has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction creation could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
