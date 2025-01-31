<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $appends = ['status'];

    protected $fillable = [
        'user_id',
        'loan_date',
        'return_date',
        'observation',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(LibraryUser::class, 'user_id');
    }

    public function copies()
    {
        return $this->belongsToMany(Copy::class, 'book_loan');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('return_date');
    }

   public function books()
    {
        return $this->belongsToMany(Book::class, 'book_loan', 'loan_id', 'copy_id')
                    ->withTimestamps();
    }

    public function getStatusAttribute()
    {
        if (!$this->return_date) {
            return 'Empréstimo';
        }

        $returnDate = Carbon::parse($this->return_date)->endOfDay();
        $today = Carbon::now()->timezone(config('app.timezone'));

        return $today->gt($returnDate) ? 'Empréstimo/Atrasado' : 'Empréstimo';
    }
}
