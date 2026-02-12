<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'status', 'employee_id'];

    // Relación: Un proyecto pertenece a un empleado
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}