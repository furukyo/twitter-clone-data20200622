<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Tweet;
use App\Models\Follower;

class UsersController extends Controller
{
    public function index(User $user)
    {
        $all_users = $user->getAllUsers(auth()->user()->id);

        return view('users.index', [
            'all_users'  => $all_users
        ]);
    }

        // フォロー
        public function follow(User $user)
        {
            $follower = auth()->user();
            // フォローしているか
            $is_following = $follower->isFollowing($user->id);
            if(!$is_following) {
                // フォローしていなければフォローする
                $follower->follow($user->id);
                return back();
            }
        }
    
        // フォロー解除
        public function unfollow(User $user)
        {
            $follower = auth()->user();
            // フォローしているか
            $is_following = $follower->isFollowing($user->id);
            if($is_following) {
                // フォローしていればフォローを解除する
                $follower->unfollow($user->id);
                return back();
            }
        }

        public function edit(User $user)
        {
            return view('users.edit', ['user' => $user]);
        }
    
        public function update(Request $request, User $user)
        {
            $data = $request->all();
            $validator = Validator::make($data, [
                'screen_name'   => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
                'name'          => ['required', 'string', 'max:255'],
                'profile_image' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)]
            ]);
            $validator->validate();
            $user->updateProfile($data);
    
            return redirect('users/'.$user->id);
        }
}