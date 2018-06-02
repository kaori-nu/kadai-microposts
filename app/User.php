<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
     public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    { 
        // 既にフォローしているかの確認 
        $exist = $this->is_following($userId); 
        // 自分自身ではないかの確認 
        $its_me = $this->id == $userId;

       if ($exist || $its_me) {

          // 既にフォローしていれば何もしない

          return false;

        } else {

           // 未フォローであればフォローする

          $this->followings()->attach($userId);

           return true;

         }
     }

    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
       // 自分自身ではないかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
          // 既にフォローしていればフォローを外す
          $this->followings()->detach($userId);
          return true;
        } else {
           // 未フォローであれば何もしない
           return false;
        }
    }

    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }

    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        // $follow_user_ids = $this->followings()->lists('users.id');
        // var_dump($follow_user_ids);
        // $follow_user_ids = $follow_user_ids->toArray();
        // var_dump($follow_user_ids);

        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'user_favorite',  'user_id', 'micropost_id')->withTimestamps();
    }

    public function favorite($micropostId)
    { 
        // 既にお気に入りしているかの確認 
        $exist = $this->is_favorites($micropostId); 

       if ($exist) {
          // 既にお気に入りしていれば何もしない
          return false;
        } else {
           // 未お気に入りであればお気に入りする
          $this->favorites()->attach($micropostId);
           return true;

         }
     }

    public function unfavorite($micropostId)
    {
        // var_dump('unfavorite -> '. $micropostId);

        // 既にお気に入りしているかの確認
        $exist = $this->is_favorites($micropostId);

        // var_dump('exist -> '. $exist);
        if ($exist) {
          // 既にお気に入りしていればお気に入りを外す
          $this->favorites()->detach($micropostId);
          return true;
        } else {
           // 未お気に入りであれば何もしない
           return false;
        }
    }
    
    public function is_favorites($micropostId)
    {
        // var_dump($this->favorites()); // テーブルの情報を表示
        // var_dump($this->favorites()->first()); // 
        // var_dump($this->favorites()->exists());
        /**
         * user_idカラム が ログインユーザID($this->id) で、
         * micropost_id が 選択された投稿ID($micropostId) のデータが
         * user_favoriteテーブルに存在するか
         */
        // 1. $this = ログインしているユーザー
        // 2. favorites = ログインしているユーザーがお気に入りしているMicropost
        // 3. where = 
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
}
