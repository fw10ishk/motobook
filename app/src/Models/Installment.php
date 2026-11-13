<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $table = 'installments';

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount_due',
        'amount_paid',
        'payment_date',
        'status'
    ];

    public function loanDetail()
    {
        return $this->belongsTo(LoanDetail::class, 'loan_id');
    }

    public function markAsPaid($amountPaid, $paymentDate, $status = 'paid')
    {
        $this->amount_paid = $amountPaid;
        $this->payment_date = $paymentDate;
        $this->status = $status;
        return $this->save();
    }
}
