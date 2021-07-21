<?php

namespace App\Http\Service;

use App\Models\Loan;

class LoanService
{
    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    /**
     * return all loans
     *
     * @method store
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     *
     */

    public function all(): ?object
    {
        return $this->loan->all();
    }

    /**
     * return stored loan object
     *
     * @method store
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     *
     */

    public function store(array $request): object
    {
        $amount = $request['amount'];
        $term = $request['term'];

        return $this->loan->create([
            'amount' => $amount,
            'term' => $term,
            'loan_balance' => $amount,
            'term_balance' => $term,
            'created_by' => auth()->user()->id,
            'created_at' => now(),
        ]);
    }

    /**
     * fetch loan record based on id
     *
     * @method store
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     *
     */

    public function get(array $request): ?object
    {
        $loanId = $request['loan'];
        return $this->loan->whereId($loanId)->with('payments')->first();
    }

     /**
     * approve pending loan
     *
     * @method approve
     *
     * @param array
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     *
     */

    public function approve($loan): object
    {
        $loan->is_approved = 1;
        $loan->save();
        return $this;
    }

    /**
     * update loan status
     *
     * @method update
     *
     * @param array
     *
     * @return bool
     *
     */

    public function update(object $loan): bool
    {
        // update loan record
        $paid = (count($loan->payments) - 1 === 0) ? 1 : 0;
        $loan->loan_balance -= $loan->emi;
        $loan->term_balance -= 1;
        $loan->updated_by = auth()->user()->id;
        $loan->updated_at = now();
        $loan->is_paid = $paid;
        $loan->save();

        return true;
    }
}
