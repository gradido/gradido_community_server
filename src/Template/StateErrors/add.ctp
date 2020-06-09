<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StateError $stateError
 */
?>
<nav id="actions-sidebar">
    <ul class="nav-horizontal nav-smaller">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List State Errors'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List State Users'), ['controller' => 'StateUsers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New State User'), ['controller' => 'StateUsers', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Transaction Types'), ['controller' => 'TransactionTypes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Transaction Type'), ['controller' => 'TransactionTypes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="stateErrors form large-9 medium-8 columns content">
    <?= $this->Form->create($stateError) ?>
    <fieldset>
        <legend><?= __('Add State Error') ?></legend>
        <?php
            echo $this->Form->control('state_user_id', ['options' => $stateUsers]);
            echo $this->Form->control('transaction_type_id', ['options' => $transactionTypes]);
            echo $this->Form->control('message_json');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
