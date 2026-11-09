<?php

namespace App\Models;

use App\Traits\Blamable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Blamable, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function sale() {
        return $this->belongsTo(Sale::class);
    }
        
    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
