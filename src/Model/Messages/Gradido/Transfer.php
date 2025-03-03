<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Transfer.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>model.messages.gradido.Transfer</code>
 */
class Transfer extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.SenderAmount senderAmounts = 1;</code>
     */
    private $senderAmounts;
    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.ReceiverAmount receiverAmounts = 2;</code>
     */
    private $receiverAmounts;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Model\Messages\Gradido\SenderAmount[]|\Google\Protobuf\Internal\RepeatedField $senderAmounts
     *     @type \Model\Messages\Gradido\ReceiverAmount[]|\Google\Protobuf\Internal\RepeatedField $receiverAmounts
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Transfer::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.SenderAmount senderAmounts = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getSenderAmounts()
    {
        return $this->senderAmounts;
    }

    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.SenderAmount senderAmounts = 1;</code>
     * @param \Model\Messages\Gradido\SenderAmount[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setSenderAmounts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Model\Messages\Gradido\SenderAmount::class);
        $this->senderAmounts = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.ReceiverAmount receiverAmounts = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getReceiverAmounts()
    {
        return $this->receiverAmounts;
    }

    /**
     * Generated from protobuf field <code>repeated .model.messages.gradido.ReceiverAmount receiverAmounts = 2;</code>
     * @param \Model\Messages\Gradido\ReceiverAmount[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setReceiverAmounts($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Model\Messages\Gradido\ReceiverAmount::class);
        $this->receiverAmounts = $arr;

        return $this;
    }

}

