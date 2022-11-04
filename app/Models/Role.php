<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'abilities',
        'restricted'
    ];


    protected $roles = [
        'Default',
        'Fondateur',
        'Directeur',
        'Censeur',
        'Censeur Adjoint',
        'Surveillant Général',
        'Surveillant Général Adjoint',
        'Teacher',
        'Secretaire',
        'Secretaire Adjoint',
        'Agent de maintenance informatique',
        'Délégué',
        'Délégué Adjoint',
        'Assistant',
        'Agent entretien',
        "Agent d'entretien",
        "Agent de garde matinal",
        "Agent de garde nocturne",
        "Aide",
        "Autres",
    ];


    public function users()
    {
        return $this->hasMany(User::class);
    }


    
    /**
     * Get the value of roles
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }


}
