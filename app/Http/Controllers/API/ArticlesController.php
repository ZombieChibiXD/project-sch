<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;
use App\Http\Resources\Article as ArticleResource;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = Article::orderBy('created_at','desc')->paginate(6);
        return ArticleResource::collection($article);
    }
    public function tag($tag)
    {
        // This show articles base on the sort that was chosen
        $article = "";
        switch($tag){
            case 'all':         
                $article = Article::orderBy('created_at','desc')->paginate(6);
                break;
            case 'popular':     
                $article = Article::orderBy('created_at','desc')->where('tag','popular')->paginate(6);
                break;
            case 'sports':      
                $article = Article::orderBy('created_at','desc')->where('tag','sports')->paginate(6);
                break;
            case 'politics':    
                $article = Article::orderBy('created_at','desc')->where('tag','politics')->paginate(6);
                break;
            default:            
                return [
                    'type'=>'error',
                    'message'=>'Tag not Found!'                                    
                ];          
        }
        return ArticleResource::collection($article);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $article = $request->isMethod('put') ? Article::findOrFail($request->input('article_id')) : new Article;
        $article->id = $request->input('article_id');
        $article->title = $request->input('title');
        $article->tag = $request->input('tag');
        $article->body = $request->input('body');
        $article->views = $request->isMethod('put')?$article->views:0;
        
        //Modify this later
        $article->user_id = $request->isMethod('put')?$article->user_id:'System';
        //$article->created_by = $request->isMethod('put')?$article->created_by:auth()->user()->id;
        if($article->save())
            return [
                'type' => 'success',
                'message' => $request->isMethod('put')?'Post has been Edited!':'Post has been Created!'
            ];
        //Idea
        //return $article->save()?
                                //[
                                    //'type'=>'success',
                                    //'message'=>$request->isMethod('put')?'Post edited!':'Post Created!'
                                // ]:[
                                //     'type'=>'error',
                                //     'message'=>'Request Failed!'                                    
                                // ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get article
        $article = Article::findOrFail($id);

        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get article
        $article = Article::findOrFail($id);
        // Deletes it
        if($article->delete()) {
            return new ArticleResource($article);
        }
    }
}