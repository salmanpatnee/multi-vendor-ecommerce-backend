<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'subtitle', 'image', 'order', 'active'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', 
    ];

    public function scopeActive($query, $active = true)
    {   if(is_null($active)) return $query;
        $query->where('is_active', '=', $active);
    }
}
