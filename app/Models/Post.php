<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory, HasTranslations;
    protected $fillable = ['title','short_description', 'content','image','slug', 'is_active'];
    protected $translatable = ['title','short_description', 'content'];
    
    // Add index information for migration
    // You should create a migration to add these indexes
    // php artisan make:migration add_indexes_to_posts_table
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function business() {
        return $this->belongsTo(Business::class);
    }
    
    public function comments() {
        return $this->hasMany(Comment::class);
    }
    
    public function likes() {
        return $this->hasMany(Like::class);
    }
    
    public function getHasLikedAttribute() {
        // Optimize by avoiding collection loading
        return $this->likes()->where('user_id', auth()->id())->exists();
    }
    
    public function toggleLike() {
        // Improved version with a single query using raw SQL
        $userId = auth()->id();
        $exists = DB::table('likes')
            ->where('user_id', $userId)
            ->where('post_id', $this->id)
            ->exists();
            
        if ($exists) {
            return DB::table('likes')
                ->where('user_id', $userId)
                ->where('post_id', $this->id)
                ->delete();
        } else {
            return DB::table('likes')->insert([
                'user_id' => $userId,
                'post_id' => $this->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    protected static function boot() {
        parent::boot();
    
        static::created(function($post){
            $post->slug = \Str::slug($post->title).'-'.$post->id;
            $post->save();
        });
    }
}
