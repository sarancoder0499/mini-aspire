<?php

declare(strict_types=1);

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
     * Return all Payments for Loan
     *
     * @method all
     *
     * @param array $request
     *
     * @return App\Models\Payment
     *
     */

    public function all(array $request): ?Payment
    {
        $loanId = $request['loan'];
        return $this->payment->whereLoanId($loanId)->get();
    }

    /**
     * Return emi amount
     *
     * @method store
     *
     * @param object $loan
     *
     * @return float
     *
     */

    public function store(object $loan): ?float
    {
        $loanId = $loan->id;
        $amount = $loan->amount;
        $term = $loan->term;
        $payment = round($amount / $term);
        $emi  = $payment;
        $days = 7;
        $data = [];

        for ($i = 0; $i < $term; $i++) {
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
     * Return re-pay paid status
     *
     * @method pay
     *
     * @param object $loan
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
