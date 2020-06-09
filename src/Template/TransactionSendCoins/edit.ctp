<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TransactionSendCoin $transactionSendCoin
 */
?>
<nav id="actions-sidebar">
    <ul class="nav-horizontal nav-smaller">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $transactionSendCoin->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $transactionSendCoin->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Transaction Send Coins'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'Transactions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List State Users'), ['controller' => 'StateUsers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State User'), ['controller' => 'StateUsers', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="transactionSendCoins form large-9 medium-8 columns content">
    <?= $this->Form->create($transactionSendCoin) ?>
    <fieldset>
        <legend><?= __('Edit Transaction Send Coin') ?></legend>
        <?php
            echo $this->Form->control('transaction_id', ['options' => $transactions]);
            echo $this->Form->control('state_user_id', ['options' => $stateUsers]);
            echo $this->Form->control('amount');
            echo $this->Form->control('sender_final_balance');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
