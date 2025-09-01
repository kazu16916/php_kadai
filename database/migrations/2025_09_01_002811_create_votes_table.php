<?php
// database/migrations/xxxx_xx_xx_create_votes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('theme_id')->constrained('themes')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('options')->onDelete('cascade');
            $table->timestamp('voted_at')->useCurrent();
            $table->timestamps();
            
            // 1ユーザーは1テーマに1回のみ投票可能
            $table->unique(['user_id', 'theme_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};