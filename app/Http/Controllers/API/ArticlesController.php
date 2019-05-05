<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;
use App\Http\Resources\Article as ArticleResource;

class ArticlesController extends Controller
{
    private $pagination_limit = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = Article::orderBy('created_at','desc')->paginate($pagination_limit);
        return ArticleResource::collection($article);
    }
    public function tag($tag)
    {
        // This show articles base on the sort that was chosen
        $article = "";
        switch($tag){
            case 'all':         
                $article = Article::orderBy('created_at','desc')->paginate($pagination_limit);
                break;
            case 'popular':     
                $article = Article::orderBy('created_at','desc')->orderBy('views','desc')->paginate($pagination_limit);
                break;
            case 'sports':      
                $article = Article::orderBy('created_at','desc')->where('tag','sports')->paginate($pagination_limit);
                break;
            case 'politics':    
                $article = Article::orderBy('created_at','desc')->where('tag','politics')->paginate($pagination_limit);
                break;
            default:  
                $article = Article::orderBy('created_at','desc')->where('tag',$tag)->paginate($pagination_limit);

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
        $isUpdate = $request->isMethod('put');      //For efficiency reasons to make this var
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:2999'
        ]);
        

        // Handle File Upload
        if($request->hasFile('cover_image')){
            // Get filename with the extension
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        } else {
            if(!$isUpdate){     // If not update
                $fileNameToStore = 'noimage.jpg';
            }
        }

        $article = $isUpdate ? Article::findOrFail($request->input('article_id')) : new Article;
        $article->id = $request->input('article_id');
        $article->title = $request->input('title');
        $article->tag = $request->input('tag');
        $article->body = $request->input('body');
        $article->views = $isUpdate ?$article->views:0;
        $article->cover_image = $fileNameToStore;
        
        //Modify this later
        $article->user_id = $isUpdate ?$article->user_id:'0';
        //$article->created_by = $request->isMethod('put')?$article->created_by:auth()->user()->id;
        $HTTP_response_code = $isUpdate ? 200:201;
        $HTTP_response_message = $isUpdate ?'Post has been Edited!':'Post has been Created!';
        if($article->save())
            return response()->json([
                'type' => 'success',
                'message' => $HTTP_response_message
            ], $HTTP_response_code);
        else {
            return response()->json([
                'type' => 'error',
                'message' => 'Action failed'
            ], 409);
        }
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
        $article->views = $article->views+1;
        $article->save();

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
            return response()->json([
                'type' => 'success',
                'message' => 'Article Deleted'
            ], 200);
        }
        else {
            return response()->json([
                'type' => 'error',
                'message' => 'Delete failed'
            ], 409);
        }
    }
}