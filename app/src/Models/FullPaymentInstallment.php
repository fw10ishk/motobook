<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FullPaymentInstallment extends Model
{
    protected $table = 'payment_installments';

    protected $fillable = [
        'full_payment_id',
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

    public function fullPayment()
    {
        return $this->belongsTo(FullPayment::class, 'full_payment_id');
    }

    public static function recordPayment($attributes)
    {
        $payment = self::create([
            'full_payment_id' => $attributes['full_payment_id'],
            'note' => $attributes['note'],
            'amount' => $attributes['amount'],
            'payment_date' => $attributes['payment_date'],
            'payment_method' => $attributes['payment_method'],
            'payment_status' => 'completed',
        ]);

        // Update the full payment detail amounts
        $payment->fullPayment->updateAmounts($attributes['amount']);

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
