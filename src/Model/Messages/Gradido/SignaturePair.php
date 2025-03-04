<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: BasicTypes.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>model.messages.gradido.SignaturePair</code>
 */
class SignaturePair extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes pubKey = 1;</code>
     */
    private $pubKey = '';
    protected $signature;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $pubKey
     *     @type string $ed25519
     *           ed25519 signature (libsodium default)
     *     @type string $ed25519_ref10
     *           ed25519 ref10 signature
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\BasicTypes::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes pubKey = 1;</code>
     * @return string
     */
    public function getPubKey()
    {
        return $this->pubKey;
    }

    /**
     * Generated from protobuf field <code>bytes pubKey = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setPubKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->pubKey = $var;

        return $this;
    }

    /**
     * ed25519 signature (libsodium default)
     *
     * Generated from protobuf field <code>bytes ed25519 = 2;</code>
     * @return string
     */
    public function getEd25519()
    {
        return $this->readOneof(2);
    }

    /**
     * ed25519 signature (libsodium default)
     *
     * Generated from protobuf field <code>bytes ed25519 = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setEd25519($var)
    {
        GPBUtil::checkString($var, False);
        $this->writeOneof(2, $var);

        return $this;
    }

    /**
     * ed25519 ref10 signature
     *
     * Generated from protobuf field <code>bytes ed25519_ref10 = 3;</code>
     * @return string
     */
    public function getEd25519Ref10()
    {
        return $this->readOneof(3);
    }

    /**
     * ed25519 ref10 signature
     *
     * Generated from protobuf field <code>bytes ed25519_ref10 = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setEd25519Ref10($var)
    {
        GPBUtil::checkString($var, False);
        $this->writeOneof(3, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->whichOneof("signature");
    }

}

