<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copy extends Model {
    use HasFactory;

    protected $fillable = ['registration_number', 'book_id', 'available'];
    public $timestamps = true;

    public function book() {
        return $this->belongsTo(Book::class);
    }

    public function loans() {
        return $this->belongsToMany(Loan::class, 'book_loan');
    }

    public function scopeLoaned($query)
    {
        return $query->whereHas('loans', function ($query) {
            $query->whereNull('return_date');
        });
    }

    public function getAvailableAttribute()
    {
        return !$this->loans()->whereNull('return_date')->exists();
    }
}
