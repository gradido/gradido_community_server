<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: TransactionCreation.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * need signature from group admin or 
 * percent of group users another than the receiver
 *
 * Generated from protobuf message <code>model.messages.gradido.TransactionCreation</code>
 */
class TransactionCreation extends \Google\Protobuf\Internal\Message
{
    /**
     * 40 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     */
    private $receiverAmount = null;
    /**
     * 4 Byte
     *
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     */
    private $ident_hash = 0;
    /**
     * 8 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.TimestampSeconds target_date = 3;</code>
     */
    private $target_date = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Model\Messages\Gradido\ReceiverAmount $receiverAmount
     *           40 Byte
     *     @type int $ident_hash
     *           4 Byte
     *     @type \Model\Messages\Gradido\TimestampSeconds $target_date
     *           8 Byte
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\TransactionCreation::initOnce();
        parent::__construct($data);
    }

    /**
     * 40 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     * @return \Model\Messages\Gradido\ReceiverAmount
     */
    public function getReceiverAmount()
    {
        return $this->receiverAmount;
    }

    /**
     * 40 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.ReceiverAmount receiverAmount = 1;</code>
     * @param \Model\Messages\Gradido\ReceiverAmount $var
     * @return $this
     */
    public function setReceiverAmount($var)
    {
        GPBUtil::checkMessage($var, \Model\Messages\Gradido\ReceiverAmount::class);
        $this->receiverAmount = $var;

        return $this;
    }

    /**
     * 4 Byte
     *
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     * @return int
     */
    public function getIdentHash()
    {
        return $this->ident_hash;
    }

    /**
     * 4 Byte
     *
     * Generated from protobuf field <code>sint32 ident_hash = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setIdentHash($var)
    {
        GPBUtil::checkInt32($var);
        $this->ident_hash = $var;

        return $this;
    }

    /**
     * 8 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.TimestampSeconds target_date = 3;</code>
     * @return \Model\Messages\Gradido\TimestampSeconds
     */
    public function getTargetDate()
    {
        return $this->target_date;
    }

    /**
     * 8 Byte
     *
     * Generated from protobuf field <code>.model.messages.gradido.TimestampSeconds target_date = 3;</code>
     * @param \Model\Messages\Gradido\TimestampSeconds $var
     * @return $this
     */
    public function setTargetDate($var)
    {
        GPBUtil::checkMessage($var, \Model\Messages\Gradido\TimestampSeconds::class);
        $this->target_date = $var;

        return $this;
    }

}

