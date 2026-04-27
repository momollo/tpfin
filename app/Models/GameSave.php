<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSave extends Model
{
    protected $fillable = [
        'user_id', 'coins', 'total_coins',
        'total_clicks', 'best_cps', 'owned_upgrades',
    ];

    protected $casts = [
        'owned_upgrades' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
