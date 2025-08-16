<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $guarded  = [];
 protected $casts = [
     'event_date' => 'datetime',
 ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Event can have many tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
