<?php

namespace App\Http\Service;

use App\Models\Payment;

class PaymentService
{
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * return all Payments for Loan
     *
     * @method store
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Payment]
     *
     */

    public function all(array $request): object
    {
        $loanId = $request['loan'];
        return $this->payment->whereLoanId($loanId)->get();
    }

    /**
     * return stored Payment object
     *
     * @method store
     *
     * @param array
     *
     * @return integer
     *
     */

    public function store(object $loan): int
    {
        $loanId = $loan->id;
        $amount = $loan->amount;
        $term = $loan->term;
        $payment = round($amount / $term);
        $emi  = $payment;
        $days = 7;
        $data = [];

        for ($i = 0; $i < $term; $i++)
        {
            //$payment = ($amount - $payment) > 0 ? $payment : $amount;
            $payment = ($i === $term - 1) ? $amount : $payment;
            $dueAt = now()->addDays($days);

            $data[] =  [
                'loan_id' => $loanId,
                'payment' => $payment,
                'due_at' => $dueAt,
                'created_by' => auth()->user()->id,
                'created_at' => now(),
            ];

            $amount = $amount - $payment;
            $days = $days + 7;
        }

        $this->payment->insert($data);
        return $emi;
    }

    /**
     * return paid status
     *
     * @method pay
     *
     * @param array
     *
     * @return bool
     *
     */

    public function pay(object $loan): bool
    {
        $payment = $loan->payments[0];

        // Pay one installment
        $payment->is_paid = 1;
        $payment->paid_at = now();
        $payment->updated_by = auth()->user()->id;
        $payment->updated_at = now();
        $payment->save();

        return true;
    }
}
