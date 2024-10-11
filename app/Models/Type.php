<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    // Relationships
    public function pokemons(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_types');
    }

    // Scopes
    public function scopeWithPokemonCount($query)
    {
        return $query->withCount('pokemons');
    }

    // Helpers
    public function getBadgeColorStyles(): string
    {
        return "background-color: {$this->color}; color: " . $this->getContrastColor($this->color);
    }

    // Método auxiliar para detectar a cor do texto baseado na cor de fundo
    public function getContrastColor(string $hexcolor): string
    {
        // Remove o # se existir
        $hexcolor = str_replace('#', '', $hexcolor);

        // Converte para RGB
        $r = hexdec(substr($hexcolor, 0, 2));
        $g = hexdec(substr($hexcolor, 2, 2));
        $b = hexdec(substr($hexcolor, 4, 2));

        // Calcula a luminosidade
        $lum = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $lum > 128 ? '#000000' : '#ffffff';
    }
}
