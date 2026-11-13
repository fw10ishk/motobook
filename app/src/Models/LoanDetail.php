<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDetail extends Model
{
    protected $table = 'loan_details';

    protected $fillable = [
        'sale_id',
        'loan_amount',
        'number_of_parts',
        'installment_amount',
        'interest_rate'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class, 'loan_id');
    }

    public static function createLoanWithInstallments($attributes)
    {
        $loanDetail = self::create($attributes);

        // Create installments based on number_of_parts
        $installmentAmount = $attributes['installment_amount'];
        $numberOfParts = $attributes['number_of_parts'];

        for ($i = 1; $i <= $numberOfParts; $i++) {
            $dueDate = date('Y-m-d H:i:s', strtotime("+$i month"));

            Installment::create([
                'loan_id' => $loanDetail->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount_due' => $installmentAmount
            ]);
        }

        return $loanDetail;
    }
}
