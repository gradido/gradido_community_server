<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Transaction.proto

namespace GPBMetadata;

class Transaction
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\BasicTypes::initOnce();
        $pool->internalAddGeneratedFile(hex2bin(
            "0ab7010a115472616e73616374696f6e2e70726f746f1207677261646964" .
            "6f2290010a0b5472616e73616374696f6e120a0a02696418012001280412" .
            "2b0a08726563656976656418022001280b32192e6772616469646f2e5469" .
            "6d657374616d705365636f6e647312250a067369674d617018032001280b" .
            "32152e6772616469646f2e5369676e61747572654d6170120e0a06747848" .
            "61736818042001280c12110a09626f6479427974657318052001280c6206" .
            "70726f746f33"
        ), true);

        static::$is_initialized = true;
    }
}

