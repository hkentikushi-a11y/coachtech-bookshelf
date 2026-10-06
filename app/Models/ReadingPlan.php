<?php

namespace App\Models;

use App\Enums\ReadingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'book_id', 'status',
        'target_date', 'started_at', 'finished_at',
    ];

    protected $casts = [
        'status' => ReadingStatus::class,
        'target_date' => 'date',
        'started_at' => 'date',
        'finished_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['want', 'reading']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'reading')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now()->toDateString());
    }

    public function scopeDueSoon($query, int $days)
    {
        return $query->where('status', 'reading')
            ->whereNotNull('target_date')
            ->whereDate('target_date', now()->addDays($days)->toDateString());
    }
}
