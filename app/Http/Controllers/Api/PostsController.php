<?php

namespace App\Http\Controllers\Api;

use App\Post;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    /**
     * Picture upload path
     *
     * @var string
     */
    protected $uploadPath = 'uploads/';

    /**
     * Fetch posts with pagination
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index() {
        return response(Post::latest()->with(['user'])->paginate(15));
    }

    /**
     * Fetch single post by id
     *
     * @param Post $post
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Post $post) {
        $post->user = $post->user()->first();
        return response($post);
    }

    /**
     * Create new post
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'picture' => 'mimes:jpg,jpeg,png'
        ]);

        if($validator->fails()) {
            return response([
                'errors' => $validator->messages()
            ], 400);
        }

        $post = new Post;
        $post->content = $request->input('content');

        if($request->hasFile('picture')) {
            $post->picture = $this->uploadPicture($request->file('picture'));
        }

        $post->save();

        $post->user = $post->user()->first();

        return response([
            'message' => 'Post was successfully updated.',
            'data' => $post
        ], 201);

    }

    /**
     * Update existent post by id
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, Post $post) {

        $validator = Validator::make($request->all(), [
            'picture' => 'mimes:jpg,jpeg,png'
        ]);

        if($validator->fails()) {
            return response([
                'errors' => $validator->messages()
            ], 400);
        }

        if($request->has('content')) {
            $post->content = $request->input('content');
        }

        if($request->hasFile('picture')) {
            File::delete($post->getOriginal('picture'));
            $post->picture = $this->uploadPicture($request->file('picture'));
        }

        $post->save();

        $post->user = $post->user()->first();

        return response([
            'message' => 'Post was successfully updated.',
            'data' => $post
        ]);

    }

    /**
     * Delete post by id
     *
     * @param Post $post
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function delete(Post $post) {
        $post->delete();

        return response([
            'message' => 'Post was successfully deleted.'
        ]);
    }

    /**
     * Upload picture
     *
     * @param $picture
     * @return string
     */
    private function uploadPicture($picture) {
        if(!File::exists($this->uploadPath)) {
            File::makeDirectory($this->uploadPath, $mode = 0777, true, true);
        }

        $imageName = time() . rand() . '.' . $picture->getClientOriginalExtension();

        Image::make($picture->getRealPath())->fit(600, 360)->save($this->uploadPath . $imageName);

        return $this->uploadPath . $imageName;
    }
}
