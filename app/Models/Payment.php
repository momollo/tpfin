<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'upgrade_id', 'upgrade_name', 'amount', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Scopes pratiques */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
