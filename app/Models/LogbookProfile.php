<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LogbookProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'logbook_profiles';

    protected static function booted()
    {
        static::addGlobalScope('excludeCashCustomer', function (Builder $builder) {

            if (!auth()->user()?->hasAnyRole(['Financier'])) {
                return;
            }

            $excludedCardCodes = [
                'C-SIA-C00015',
                'C-MRF-C00015',
                'C-ENG-C00015',
                'C-ITL-C00015',
                'C-LSE-C00015',
                'C-ASS-C00015',
                'C-TLB-C00015',
                'C-DOO-C00015',
                'C-KTL-C00015',
                'C-GAR-C00015',
                'C-DIST-C00015',
                'C-NYK-C00015',
                'C-KER-C00015',
                'C-IR-C00015',
                'C-BUG-C00015',
                'C-MSA-C00015',
                'C-KIS-C00015',
                'C-KSM-C00015',
                'C-THK-C00015',
                'C-KIT-C00015',
                'C-NKR-C00015',
                'C-VOI-C00015',
                'C-ELD-C00015',
                'C-MAL-C00015',
                'C-TRD-C00015',
                'C-KUB-C00015',

                'C-TRD-S000298',
                'C-DIST-B00012',
                'C-DIST-A00007',
            ];

            $builder->whereNotIn('CardCode', $excludedCardCodes);
        });

        static::addGlobalScope('directTransferShouldBeVisibleOnlyToSuperAdmin', function ($builder) {
            if (auth()->user()?->hasAnyRole(['SuperAdmin'])) {
                return;
            }

            $builder->where(function ($q) {
                $q->whereNull('groupCode')
                    ->orWhereNotIn('groupCode', ['direct_transfer']);
            });

        });



        static::addGlobalScope('cleanChasis', function ($builder) {
            $builder->where('chasisNumber', 'not like', '%.%');
        });

        static::addGlobalScope('onlyStatus', function ($builder) {
            $builder->whereIn('status', [1, 2, 3, 4, 5, 6, 7]);
        });

        static::addGlobalScope('onlyChasisLinkedToPin', function ($builder) {

            if (!auth()->user()?->hasAnyRole(['Dealer', 'Customer'])) {
                return;
            }

            $builder->where('PinNo', auth()->user()?->pin_no);
        });
    }

    public function scopeUniqueChasis(Builder $query): Builder
    {
        return $query->whereIn('id', function ($q) {
            $q->selectRaw('MIN(id)')
                ->from('logbook_profiles')
                ->groupBy('chasisNumber');
        });
    }

    public function logbookLocation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Location', 'location');
    }

    public function logbookOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PinNo', 'pin_no');
    }

    public function request(): HasOne
    {
        return $this->hasOne(LogbookRequest::class, 'chasisNumber', 'chasisNumber');
    }

    public function requestProfile(): BelongsTo
    {
        return $this->belongsTo(LogbookRequest::class, 'chasisNumber', 'chasisNumber');
    }

    public function system_status(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'status', 'ObjType');
    }
}
