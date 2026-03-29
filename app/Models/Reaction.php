<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class Reaction extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'meme_id',
        'user_id',
        'emoji',
    ];

    // Relation with Meme
    public function meme()
    {
        return $this->belongsTo(Meme::class);
    }

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->unique(['user_id', 'meme_id']); // user ek post me sirf ek emoji
        });
    }

    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'meme_id']);
        });
    }
};
