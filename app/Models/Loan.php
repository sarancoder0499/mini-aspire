<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    /**
     * The attributes that should guard from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * Return all Payments object with the loan id
     *
     *
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Payment]
     *
     */
    public function payments(): object
    {
        return $this->hasMany(Payment::class, 'loan_id')->where('is_paid', false)->orderBy('due_at', 'asc');
    }
}
