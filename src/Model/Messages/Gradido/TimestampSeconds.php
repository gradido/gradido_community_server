<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: BasicTypes.proto

namespace Model\Messages\Gradido;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * An exact date and time,  with a resolution of one second (no nanoseconds). 
 *
 * Generated from protobuf message <code>model.messages.gradido.TimestampSeconds</code>
 */
class TimestampSeconds extends \Google\Protobuf\Internal\Message
{
    /**
     * Number of complete seconds since the start of the epoch
     *
     * Generated from protobuf field <code>int64 seconds = 1;</code>
     */
    private $seconds = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $seconds
     *           Number of complete seconds since the start of the epoch
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\BasicTypes::initOnce();
        parent::__construct($data);
    }

    /**
     * Number of complete seconds since the start of the epoch
     *
     * Generated from protobuf field <code>int64 seconds = 1;</code>
     * @return int|string
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * Number of complete seconds since the start of the epoch
     *
     * Generated from protobuf field <code>int64 seconds = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setSeconds($var)
    {
        GPBUtil::checkInt64($var);
        $this->seconds = $var;

        return $this;
    }

}

