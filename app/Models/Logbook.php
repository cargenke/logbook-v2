<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Logbook extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'logbooks';

    public function system_status(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'status', 'ObjType');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(LogbookRequest::class, 'id', 'logbook_id');
    }

    // public function request() : HasOne
    // {
    //     return $this->hasOne(LogbookRequest::class, 'logbook_id');
    // }

    public function logbook_profile(): BelongsTo
    {
        return $this->belongsTo(LogbookProfile::class, 'id', 'logbook_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(LogbookProfile::class, 'chasisNumber', 'chasisNumber');
    }

    // public function logbook_profile() : HasOne
    // {
    //     return $this->hasOne(LogbookProfile::class, 'logbook_id');
    // }

}
