<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerFullPaymentInstallment extends Model
{
    protected $table = 'dealer_payment_installments';

    protected $fillable = [
        'dealer_full_payment_id',
        'note',
        'amount',
        'payment_date',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function dealerFullPayment()
    {
        return $this->belongsTo(DealerFullPayment::class, 'dealer_full_payment_id');
    }

    public static function recordPayment($attributes)
    {
        $payment = self::create([
            'dealer_full_payment_id' => $attributes['dealer_full_payment_id'],
            'note' => $attributes['note'],
            'amount' => $attributes['amount'],
            'payment_date' => $attributes['payment_date'],
            'payment_method' => $attributes['payment_method'],
            'payment_status' => 'completed',

        ]);

        // Update the full payment detail amounts
        $payment->dealerFullPayment->updateAmounts($attributes['amount']);

        return $payment;
    }

    public static function updatePayment($id, $attributes)
    {
        $payment = self::find($id);

        $previousAmount = $payment->amount;
        $difference = $attributes['amount'] - $previousAmount;

        if ($difference > 0) {
            $payment->fullPayment->updateAmounts($difference);
        } else {
            $payment->fullPayment->reduceAmount(abs($difference));
        }

        $payment->note = $attributes['note'];
        $payment->amount = $attributes['amount'];
        $payment->payment_date = $attributes['payment_date'];
        $payment->payment_method = $attributes['payment_method'];

        $payment->save();

        return $payment;
    }
}
