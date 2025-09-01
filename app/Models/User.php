<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', // nameからusernameに変更
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed', // Laravel 10+の自動ハッシュ化
    ];

    /**
     * ユーザーが作成したテーマとのリレーション
     */
    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class, 'creator_id');
    }

    /**
     * ユーザーの投票とのリレーション
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * 認証に使用するユーザー名フィールドを指定
     * デフォルトの'email'から'username'に変更
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}