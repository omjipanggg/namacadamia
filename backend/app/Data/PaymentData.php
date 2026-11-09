<?php

namespace App\Data;

use App\Models\Sale;

class PaymentData
{
    public function __construct(
        public Sale $sale,
        public float $receivedAmount,
        public float $paymentAmount,
        public float $changeAmount,
    ) {}
}