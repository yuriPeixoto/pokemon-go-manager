<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function favoritePokemons(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'favorite_pokemons')->withTimestamps();
    }

    // Helpers
    public function toggleFavorite(Pokemon $pokemon): void
    {
        $this->favoritePokemons()->toggle($pokemon);
    }

    public function hasFavorited(Pokemon $pokemon): bool
    {
        return $this->favoritePokemons()->where('pokemon_id', $pokemon->id)->exists();
    }

    // Scopes
    public function scopeWithFavoritePokemonCount($query)
    {
        return $query->withCount('favoritePokemons');
    }
}
