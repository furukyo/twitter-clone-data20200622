<?php
namespace App\Models;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->unsignedInteger('following_id')->comment('フォローしているユーザID');
            $table->unsignedInteger('followed_id')->comment('フォローされているユーザID');

            $table->index('following_id');
            $table->index('followed_id');

            $table->unique([
                'following_id',
                'followed_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}




use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $primaryKey = [
        'following_id',
        'followed_id'
    ];
    protected $fillable = [
        'following_id',
        'followed_id'
    ];
    public $timestamps = false;
    public $incrementing = false;


    // フォローしているユーザのIDを取得
    public function followingIds(Int $user_id)
    {
        return $this->where('following_id', $user_id)->get('followed_id');
    }

        // 一覧画面
        public function getTimeLine(Int $user_id, Array $follow_ids)
        {
            // 自身とフォローしているユーザIDを結合する
            $follow_ids[] = $user_id;
            return $this->whereIn('user_id', $follow_ids)->orderBy('created_at', 'DESC')->paginate(50);
        }
        public function getEditTweet(Int $user_id, Int $tweet_id)
        {
            return $this->where('user_id', $user_id)->where('id', $tweet_id)->first();
        }

        public function getFollowCount($user_id)
        {
            return $this->where('following_id', $user_id)->count();
        }
    
        public function getFollowerCount($user_id)
        {
            return $this->where('followed_id', $user_id)->count();
        }
}