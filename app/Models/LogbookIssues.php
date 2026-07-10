<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookIssues extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'logbook_issues';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }
}
