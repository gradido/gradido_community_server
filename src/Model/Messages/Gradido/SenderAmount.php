<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: BasicTypes.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>model.messages.gradido.SenderAmount</code>
 */
class SenderAmount extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes ed25519_sender_pubkey = 1;</code>
     */
    private $ed25519_sender_pubkey = '';
    /**
     * Generated from protobuf field <code>sint64 amount = 2;</code>
     */
    private $amount = 0;
    /**
     * sender balance after transaction, including perishability
     *
     * Generated from protobuf field <code>sint64 senderFinalBalance = 3;</code>
     */
    private $senderFinalBalance = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $ed25519_sender_pubkey
     *     @type int|string $amount
     *     @type int|string $senderFinalBalance
     *           sender balance after transaction, including perishability
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\BasicTypes::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes ed25519_sender_pubkey = 1;</code>
     * @return string
     */
    public function getEd25519SenderPubkey()
    {
        return $this->ed25519_sender_pubkey;
    }

    /**
     * Generated from protobuf field <code>bytes ed25519_sender_pubkey = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setEd25519SenderPubkey($var)
    {
        GPBUtil::checkString($var, False);
        $this->ed25519_sender_pubkey = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>sint64 amount = 2;</code>
     * @return int|string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Generated from protobuf field <code>sint64 amount = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setAmount($var)
    {
        GPBUtil::checkInt64($var);
        $this->amount = $var;

        return $this;
    }

    /**
     * sender balance after transaction, including perishability
     *
     * Generated from protobuf field <code>sint64 senderFinalBalance = 3;</code>
     * @return int|string
     */
    public function getSenderFinalBalance()
    {
        return $this->senderFinalBalance;
    }

    /**
     * sender balance after transaction, including perishability
     *
     * Generated from protobuf field <code>sint64 senderFinalBalance = 3;</code>
     * @param int|string $var
     * @return $this
     */
    public function setSenderFinalBalance($var)
    {
        GPBUtil::checkInt64($var);
        $this->senderFinalBalance = $var;

        return $this;
    }

}

