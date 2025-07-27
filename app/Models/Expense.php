<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'deputy_id',
        'year',
        'month',
        'expense_type',
        'document_code',
        'document_type',
        'document_type_code',
        'document_date',
        'document_number',
        'gross_value',
        'document_url',
        'supplier_name',
        'supplier_cnpj_cpf',
        'net_value',
        'glosa_value',
        'reimbursement_number',
        'batch_code',
        'installment',
    ];

    protected $casts = [
        'document_date' => 'date',
        'gross_value' => 'float',
        'net_value' => 'float',
        'glosa_value' => 'float',
    ];

    public function deputy()
    {
        return $this->belongsTo(Deputy::class, 'deputy_id');
    }
}
