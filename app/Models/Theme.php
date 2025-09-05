<?php
// app/Models/Theme.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// 必要なモデルクラスを明示的にインポート
use App\Models\User;
use App\Models\Category;
use App\Models\Option;
use App\Models\Vote;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'category_id',
    ];
    protected $casts = [
    'creator_id'  => 'integer',
    'category_id' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }
}