<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: BasicTypes.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>model.messages.gradido.ReceiverAmount</code>
 */
class ReceiverAmount extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes ed25519_receiver_pubkey = 1;</code>
     */
    private $ed25519_receiver_pubkey = '';
    /**
     * Generated from protobuf field <code>sint64 amount = 2;</code>
     */
    private $amount = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $ed25519_receiver_pubkey
     *     @type int|string $amount
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\BasicTypes::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes ed25519_receiver_pubkey = 1;</code>
     * @return string
     */
    public function getEd25519ReceiverPubkey()
    {
        return $this->ed25519_receiver_pubkey;
    }

    /**
     * Generated from protobuf field <code>bytes ed25519_receiver_pubkey = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setEd25519ReceiverPubkey($var)
    {
        GPBUtil::checkString($var, False);
        $this->ed25519_receiver_pubkey = $var;

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

}

