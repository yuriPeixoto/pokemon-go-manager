<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pokemon extends Model
{
    use HasFactory;

    protected $fillable = [
        'pokedex_number',
        'name',
        'cp',
        'hp',
        'weight',
        'height',
        'is_shiny',
        'is_best_buddy',
        'iv_percentage',
        'iv_attack',
        'iv_defense',
        'iv_stamina',
        'image_url',
    ];

    protected $casts = [
        'is_shiny'      => 'boolean',
        'is_best_buddy' => 'boolean',
        'iv_percentage' => 'decimal:2',
        'weight'        => 'float',
        'height'        => 'float',
    ];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'pokemon_types');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_pokemons')->withTimestamps();
    }

    public function getIvRating(): string
    {
        return match (true) {
            $this->iv_percentage = 100 => 'Perfect',
            $this->iv_percentage >= 96 => 'Elite',
            $this->iv_percentage >= 80 => 'Super',
            default => 'Common',
        };
    }

    public function calculateIvPercentage(): float
    {
        $total = $this->iv_attack + $this->iv_defense + $this->iv_stamina;
        $maxTotal = 45;
        return round(($total / $maxTotal) * 100, 2);
    }

    public function validateIvs(): bool
    {
        return $this->iv_attack >= 0 && $this->iv_attack <= 15 &&
               $this->iv_defense >= 0 && $this->iv_defense <= 15 &&
               $this->iv_stamina >= 0 && $this->iv_stamina <= 15;
    }

    // Boot method para cálculos automáticos
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Pokemon $pokemon) {
            if (!$pokemon->iv_percentage) {
                $pokemon->iv_percentage = $pokemon->calculateIvPercentage();
            }
        });
    }

    // Scopes para consultas comuns
    public function scopePerfectIv($query)
    {
        return $query->where('iv_percentage', 100);
    }

    public function scopeBestBuddies($query)
    {
        return $query->where('is_best_buddy', true);
    }

    public function scopeShiny($query)
    {
        return $query->where('is_shiny', true);
    }

    public function scopeOrderByCp($query, $direction = 'desc')
    {
        return $query->orderBy('cp', $direction);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
