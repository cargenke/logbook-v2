<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'logbook_requests';

    public function profile(): BelongsTo
    {
        return $this->belongsTo(LogbookProfile::class, 'chasisNumber', 'chasisNumber');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }

    public function assignto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_to', 'id');
    }

    public function logbook_issues(): BelongsTo
    {
        return $this->belongsTo(LogbookIssues::class, 'id', 'requesterId');
    }
}
