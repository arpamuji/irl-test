<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'src_code',
        'name',
        'short_name',
        'start_date',
        'end_date',
        'monthly_value',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'monthly_value' => 'decimal:4',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
