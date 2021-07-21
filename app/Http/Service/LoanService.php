<?php

declare(strict_types=1);

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
     * Return all loans
     *
     * @method all
     *
     * @return App\Models\Loan
     *
     */

    public function all(): ?Loan
    {
        return $this->loan->all();
    }

    /**
     * Create new loan and return it
     *
     * @method store
     *
     * @param array $request
     *
     * @return App\Models\Loan
     *
     */

    public function store(array $request): ?Loan
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
     * Fetch loan record based on id
     *
     * @method get
     *
     * @param array $request
     *
     * @return App\Models\Loan
     *
     */

    public function get(array $request): ?Loan
    {
        $loanId = $request['loan'];
        return $this->loan->whereId($loanId)->with('payments')->first();
    }

     /**
     * Approve pending loan
     *
     * @method approve
     *
     * @param array $loan
     *
     * @return App\Models\Loan
     *
     */

    public function approve($loan): ?Loan
    {
        $loan->is_approved = 1;
        $loan->save();
        return $loan;
    }

    /**
     * Update loan status
     *
     * @method update
     *
     * @param object $loan
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
