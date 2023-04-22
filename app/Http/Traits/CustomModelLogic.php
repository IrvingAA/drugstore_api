<?php

namespace App\Http\Traits;
use App\Casts\CleanText;
/**
 * Created by PhpStorm.
 * User: michelnogales@gmail.com
 * Date: 8/26/19
 * Time: 2:04 PM
 */
trait CustomModelLogic
{
    /**
     * @var bool
     */
    public static $withoutAppends = true;

    /**
     * Check if $withoutAppends is enabled.
     *
     * @return array
     */


    protected function getArrayableAppends()
    {
        if (self::$withoutAppends) {
            return [];
        }
        return parent::getArrayableAppends();
    }

    public function getFullNameAttribute(): string
    {
        return "$this->name $this->first_name $this->second_name";
    }

    public function getCatalogNameAttribute()
    {
        return static::class;
    }

    function getHashIdAttribute()
    {
        return encrypt($this->id);
    }

    function scopeSearchUsers($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->when(!empty($search), function ($query) use ($search) {
                return $query->where('username', $search)
                    ->orWhere('email', 'ILIKE', '%' . $search . '%')
                    ->orWhere('first_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('second_name', 'ILIKE', '%' . $search . '%')
                    ->orWhereHas('profile', function ($qq) use ($search) {
                        return $qq->where('name', 'ILIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('unity', function ($qq) use ($search) {
                        return $qq->where('name', 'ILIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('area', function ($qq) use ($search) {
                        return $qq->where('name', 'ILIKE', '%' . $search . '%');
                    })
                    ->orWhere(\DB::raw("concat(name,' ', \"first_name\",' ', \"second_name\")"),
                        'ILIKE', '%' . $search . '%');
            });
        });
    }

    function scopeSearchTitular($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->when(!empty($search), function ($query) use ($search) {
                return $query->where('name', $search)
                    ->orWhere('first_name', 'ILIKE', '%' . $search . '%')
                    ->orWhere('second_name', 'ILIKE', '%' . $search . '%')
                    ->orWhereHas('nationality', function ($qq) use ($search) {
                        return $qq->where('name', 'ILIKE', '%' . $search . '%');
                    })
                    ->orWhere(\DB::raw("concat(name,' ', \"first_name\",' ', \"second_name\")"),
                        'ILIKE', '%' . $search . '%');
            });
        });
    }

}
