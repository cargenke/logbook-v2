<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadProcessLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'upload_process_logs';

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
