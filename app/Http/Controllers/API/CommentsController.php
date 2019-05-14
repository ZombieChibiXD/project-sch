<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Comment;
use App\Article;

class CommentsController extends Controller
{
    
    private $user_data = null;
    private $user_exist = null;
    private $request = null;
    
    public function checkUser(Request $request) {
        $this->request = $request;
        $this->user_data = $this->request->user();
        if($this->user_data)
            $this->user_exist = $this->user_data->id > 0 ? true : false;
    }
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index()
    // {
    //     $comments =  Comment::all();
    //     return $comments;
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id,Request $request)
    {
        $this->checkUser($request);
        $comment = new Comment;
        $comment->body = $request->get('comment_body');
        $comment->user()->associate($this->user_data);
        $article = Article::find($id);
        if($article->comments()->save($comment))
            return response()->json([
                "type"=>"success",
                "message"=>"You have posted your comment"
            ], 201);
        else
            return response()->json([
                "type"=>"error",
                "message"=>"Error on posting your comment"
            ], 409);
        //Include return
    }
    public function update($id,Request $request)
    {
        $this->checkUser($request);
        $comment = Comment::findOrFail($id);
        $comment->body = $request->get('comment_body');
        if($comment->user_id == $this->user_data->id || $this->user_data->level<3){
            if($comment->save())
                return response()->json([
                    "type"=>"success",
                    "message"=>"You have edited your comment"
                ], 203);
            else
                return response()->json([
                    "type"=>"error",
                    "message"=>"Error on editing your comment"
                ], 409);
        }
        else{
            return response()->json([
                "type"=>"error",
                "message"=>"Unauthorized"
            ], 401);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $this->checkUser($request);
        $comment = Comment::findOrFail($request->input('comment_id'));
        if($comment->user_id == $this->user_data->id || $this->user_data->level<3){
            if($comment->delete())
                return response()->json([
                    "type"=>"success",
                    "message"=>"You have deleted your comment"
                ], 200);
            else
                return response()->json([
                    "type"=>"error",
                    "message"=>"Error on deleting your comment"
                ], 500);
        }
        else{
            return response()->json([
                "type"=>"error",
                "message"=>"Unauthorized"
            ], 401);
        }
    }
}
