<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountJournal extends Model
{
    use HasFactory;
    protected $table = 'account_journal';
    protected $primaryKey = 'journal_id';
    public $timestamps = false;

    protected $fillable = [
        'acc_id',
        'journal_details',
        'currency_rate',
        'currency_id',
        'entry_date',
        'type',
    ];
}
