<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deputy extends Model
{
    use HasFactory;

    protected $table = 'deputies';

    protected $fillable = [
        'id',
        'name',
        'party_abbr',
        'state_abbr',
        'uri',
        'party_uri',
        'legislature_id',
        'photo_url',
        'email',
    ];

    public $incrementing = false;

    protected $keyType = 'int';

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'deputy_id');
    }
}
