<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: TransactionBody.proto

namespace GPBMetadata;

class TransactionBody
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Transfer::initOnce();
        \GPBMetadata\StateCreateGroup::initOnce();
        \GPBMetadata\StateGroupChangeParent::initOnce();
        \GPBMetadata\TransactionCreation::initOnce();
        $pool->internalAddGeneratedFile(hex2bin(
            "0aec020a155472616e73616374696f6e426f64792e70726f746f12076772" .
            "616469646f1a16537461746543726561746547726f75702e70726f746f1a" .
            "1c537461746547726f75704368616e6765506172656e742e70726f746f1a" .
            "195472616e73616374696f6e4372656174696f6e2e70726f746f22f0010a" .
            "0f5472616e73616374696f6e426f6479120c0a046d656d6f180120012809" .
            "12300a0b63726561746547726f757018022001280b32192e677261646964" .
            "6f2e537461746543726561746547726f75704800123c0a1167726f757043" .
            "68616e6765506172656e7418032001280b321f2e6772616469646f2e5374" .
            "61746547726f75704368616e6765506172656e74480012250a087472616e" .
            "7366657218042001280b32112e6772616469646f2e5472616e7366657248" .
            "0012300a086372656174696f6e18052001280b321c2e6772616469646f2e" .
            "5472616e73616374696f6e4372656174696f6e480042060a046461746162" .
            "0670726f746f33"
        ), true);

        static::$is_initialized = true;
    }
}

