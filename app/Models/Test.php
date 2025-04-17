<?php

namespace App\Models;

use App\Models\Traits\HasOwner;
use App\Observers\TestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([TestObserver::class])]
class Test extends Model
{
    use HasFactory, HasOwner;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_active' => 'boolean',
            'next_run_at' => 'datetime',
        ];
    }
}