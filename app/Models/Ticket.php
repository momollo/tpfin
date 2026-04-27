<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description',
        'status', 'category', 'admin_reply',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Scopes */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'open'        => '🟢 Ouvert',
            'in_progress' => '🟡 En cours',
            'closed'      => '🔴 Fermé',
            default       => $this->status,
        };
    }
}
