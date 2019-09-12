<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TransactionGroupAllowtrade Entity
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $group_id
 * @property bool $allow
 *
 * @property \App\Model\Entity\Transaction $transaction
 * @property \App\Model\Entity\Group $group
 */
class TransactionGroupAllowtrade extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'transaction_id' => true,
        'group_id' => true,
        'allow' => true,
        'transaction' => true,
        'group' => true
    ];
}
