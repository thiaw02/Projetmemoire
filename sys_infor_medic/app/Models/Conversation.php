<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Conversation extends Model
{
    protected $fillable = ['user_one_id','user_two_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userOne() { return $this->belongsTo(User::class, 'user_one_id'); }
    public function userTwo() { return $this->belongsTo(User::class, 'user_two_id'); }

    public static function between(int $a, int $b): ?self
    {
        $min = min($a,$b); $max = max($a,$b);
        return static::where('user_one_id',$min)->where('user_two_id',$max)->first();
    }

    public static function ensure(int $a, int $b): self
    {
        $min = min($a,$b); $max = max($a,$b);
        return static::firstOrCreate(['user_one_id'=>$min,'user_two_id'=>$max]);
    }
}
