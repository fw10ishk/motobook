<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerFullPayment extends Model
{
    protected $table = 'dealer_full_payment_details';

    protected $fillable = [
        'sale_id',
        'total_amount',
        'amount_paid',
        'remaining_amount'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(DealerFullPaymentInstallment::class, 'dealer_full_payment_id');
    }

    public static function createDealerFullPayment($attributes)
    {
        return self::create([
            'sale_id' => $attributes['sale_id'],
            'total_amount' => $attributes['total_price'],
            'amount_paid' => $attributes['amount_paid'] ?? 0,
            'remaining_amount' => $attributes['total_price']
        ]);
    }

    public function updateAmounts($paymentAmount)
    {
        $this->amount_paid += $paymentAmount;
        $this->remaining_amount = $this->total_amount - $this->amount_paid;
        $this->save();
    }

    public function reduceAmount($paymentAmount)
    {
        $this->amount_paid -= $paymentAmount;
        $this->remaining_amount = $this->total_amount - $this->amount_paid;
        $this->save();
    }

    public static function recordPayment($attributes)
    {
        $payment = DealerFullPaymentInstallment::recordPayment([
            'dealer_full_payment_id' => $attributes['dealer_full_payment_id'],
            'note' => $attributes['note'],
            'amount' => $attributes['amount'],
            'payment_date' => $attributes['payment_date'],
            'payment_method' => $attributes['payment_method']
        ]);

        return $payment;
    }

    public static function updatePayment($id, $attributes)
    {
        $payment = DealerFullPaymentInstallment::updatePayment($id, [
            'note' => $attributes['note'],
            'amount' => $attributes['amount'],
            'payment_date' => $attributes['payment_date'],
            'payment_method' => $attributes['payment_method'],
            'notes' => $attributes['notes']
        ]);

        return $payment;
    }
}
