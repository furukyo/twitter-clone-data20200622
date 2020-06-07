<?php
namespace App\Models;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('ユーザID');
            $table->string('text')->comment('本文');
            $table->softDeletes();
            $table->timestamps();

            $table->index('id');
            $table->index('user_id');
            $table->index('text');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tweets');
    }

}



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Tweet extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text'
    ];



public function user()
{
        return $this->belongsTo(User::class);
}

public function favorites()
{
        return $this->hasMany(Favorite::class);
}

 public function comments()
{
        return $this->hasMany(Comment::class);
}

    // 一覧画面
    public function getTimeLines(Int $user_id, Array $follow_ids)
    {
        // 自身とフォローしているユーザIDを結合する
        $follow_ids[] = $user_id;
        return $this->whereIn('user_id', $follow_ids)->orderBy('created_at', 'DESC')->paginate(50);
    }

        // 詳細画面
        public function getTweet(Int $tweet_id)
        {
            return $this->with('user')->where('id', $tweet_id)->first();
        }

        public function tweetStore(Int $user_id, Array $data)
        {
            $this->user_id = $user_id;
            $this->text = $data['text'];
            $this->save();
    
            return;
        }

        public function tweetUpdate(Int $tweet_id, Array $data)
        {
            $this->id = $tweet_id;
            $this->text = $data['text'];
            $this->update();
    
            return;
        }

        public function tweetDestroy(Int $user_id, Int $tweet_id)
        {
            return $this->where('user_id', $user_id)->where('id', $tweet_id)->delete();
        }

        
}