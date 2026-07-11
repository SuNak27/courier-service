<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    /** @use HasFactory<\Database\Factories\CourierFactory> */
    use HasFactory;

    /**
     * The lowest and highest level a courier may have.
     */
    public const MIN_LEVEL = 1;

    public const MAX_LEVEL = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle_type',
        'vehicle_plate',
        'level',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Filter couriers whose name contains every word in the search term.
     *
     * Search "budi agung" will match a courier named "Budiono Hadi Agung",
     * because the name contains both "budi" and "agung".
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        // Pisah kata per spasi lalu tambahkan kondisi LIKE untuk tiap kata.
        foreach (explode(' ', $term) as $word) {
            $word = trim($word);

            if ($word !== '') {
                $query->where('name', 'like', '%'.$word.'%');
            }
        }

        return $query;
    }

    /**
     * Filter couriers by a comma-separated list of levels, e.g. "2,3".
     */
    public function scopeLevels(Builder $query, ?string $levels): Builder
    {
        if (empty($levels)) {
            return $query;
        }

        // "2,3" => [2, 3]
        $levels = array_map('intval', explode(',', $levels));

        return $query->whereIn('level', $levels);
    }
}
