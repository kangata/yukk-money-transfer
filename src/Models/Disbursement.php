<?php

namespace QuetzalStudio\YUKK\MoneyTransfer\Models;

use Illuminate\Contracts\Support\Arrayable;

class Disbursement implements Arrayable
{
    public function __construct(
        protected string $orderId,
        protected string $bankCode,
        protected string $accountNumber,
        protected int $amount,
        protected ?string $remark = null,
    )
    {
        //
    }

    public function toArray()
    {
        return [
            'order_id' => $this->orderId,
            'bank_code' => $this->bankCode,
            'account_number' => $this->accountNumber,
            'amount' => $this->amount,
            'remark' => $this->remark,
        ];
    }
}
