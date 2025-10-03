<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\CommonQueryScopes;

class Event extends Model
{
    use HasFactory, CommonQueryScopes;

    protected $fillable = [
        'title','description','date','location','created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
