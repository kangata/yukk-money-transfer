<?php

namespace QuetzalStudio\YUKK\MoneyTransfer\Models;

use Illuminate\Contracts\Support\Arrayable;

class BankAccountInquiry implements Arrayable
{
    public function __construct(
        protected string $bankCode,
        protected string $accountNumber,
        protected string $holderName,
    )
    {
        //
    }

    public function toArray()
    {
        return [
            'bank_code' => $this->bankCode,
            'account_number' => $this->accountNumber,
            'holder_name' => $this->holderName,
        ];
    }
}
