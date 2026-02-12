<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'position', 'phone', 'tags', 'status', 'user_id'
    ];

    // Buscador por Nombre, Email o Etiquetas
    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        $query->where(function ($q) use ($term) {
            $q->where('full_name', 'like', $term)
              ->orWhere('email', 'like', $term)
              ->orWhere('tags', 'like', $term);
        });
    }

    // Relación con el Administrador encargado
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con los proyectos asignados
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}