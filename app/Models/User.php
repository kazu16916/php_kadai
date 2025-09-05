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
        'username',
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
        'password' => 'hashed',
    ];

    /**
     * 認証に使用するユーザー名フィールドを指定
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * 認証識別子の値を取得
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * 認証用パスワードを取得
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Remember tokenの取得
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Remember tokenの設定
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Remember tokenのカラム名
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

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
}
