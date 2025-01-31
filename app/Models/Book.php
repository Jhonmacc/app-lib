<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     *
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'author',
        'description',
        'image',
        'genres',
    ];

    /**
     *
     *
     * @var array
     */
    protected $casts = [
        'genres' => 'array',
    ];

    /**
     *
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     *
     *
     * @return string
     */
    public function getGenresStringAttribute()
    {
        return implode(', ', $this->genres ?? []);
    }

        public function getStatusAttribute()
    {
        return $this->availableCopies()->exists() ? 'Disponível' : 'Indisponível';
    }

    public function copies()
    {
        return $this->hasMany(Copy::class);
    }

    public function availableCopies()
    {
        return $this->hasMany(Copy::class)->whereDoesntHave('loans', function ($query) {
            $query->whereNull('return_date');
        });
    }

    public function getAvailableCopiesCountAttribute()
    {
        return $this->copies()->where('available', true)->count();
    }
}
