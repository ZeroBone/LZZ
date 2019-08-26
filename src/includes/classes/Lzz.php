<?php


namespace Lzz;

use Lzz\Account\Account;

class Lzz {

    public $account;

    public function __construct() {

        $this->account = new Account();

    }

}