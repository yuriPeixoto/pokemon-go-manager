<?php

namespace App\Livewire;

use App\Models\Pokemon;
use App\Models\Type;
use Livewire\Volt\Component;
use Livewire\WithPagination;

class PokemonList extends Component
{
    use WithPagination;

    public string $search          = '';
    public ?int $typeFilter        = null;
    public string $sortField       = 'cp';
    public string $sortDirection   = 'desc';
    public bool $showOnlyFavorites = false;
    public bool $showOnlyShiny     = false;
    public bool $showOnlyBestBuddy = false;

    public function with(): array
    {
        $query = Pokemon::query()
            ->with(['types'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('pokemon_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->ofType($this->typeFilter);
            })
            ->when($this->showOnlyFavorites, function ($query) {
                $query->whereHas('favoritedBy', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            })
            ->when($this->showOnlyShiny, function ($query) {
                $query->shiny();
            })
            ->when($this->showOnlyBestBuddy, function ($query) {
                $query->bestBuddies();
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return [
            'pokemons' => $query->paginate(12),
            'types' => Type::all(),
        ];
    }

    public function toggleFavorite(Pokemon $pokemon): void
    {
        auth()->user()->toggleFavorite($pokemon);
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'typeFilter', 'showOnlyFavorites', 'showOnlyShiny', 'showOnlyBestBuddy',
        ]);

        $this->search = '';
    }
}
