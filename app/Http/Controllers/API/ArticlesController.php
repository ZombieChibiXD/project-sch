<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;
use App\Http\Resources\Article as ArticleResource;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = Article::orderBy('created_at','desc')->paginate(10);
        return ArticleResource::collection($article);
    }
    public function tag($tag)
    {
        // This show articles base on the sort that was chosen
        $article = "";
        switch($tag){
            case 'all':         
                $article = Article::orderBy('created_at','desc')->paginate(10);
                break;
            case 'popular':     
                $article = Article::orderBy('created_at','desc')->orderBy('views','desc')->paginate(10);
                break;
            case 'sports':      
                $article = Article::orderBy('created_at','desc')->where('tag','sports')->paginate(10);
                break;
            case 'politics':    
                $article = Article::orderBy('created_at','desc')->where('tag','politics')->paginate(10);
                break;
            default:  
                $article = Article::orderBy('created_at','desc')->where('tag',$tag)->paginate(10);

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
        
        $user= $request->user();
        if($user->id>0){
            // Handle File Upload
            if($request->hasFile('cover_image')){
                // Get filename with the extension
                $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore= time().'_'.$filename.'.'.$extension;
                // Upload Image
                $path = $request->file('cover_image')->storeAs('public/img/cover_images', $fileNameToStore);
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
            
            $oldId = $article->user_id;
            //Modify this later
            $article->user_id = $isUpdate ?$article->user_id:$user->id;
            //$article->created_by = $request->isMethod('put')?$article->created_by:auth()->user()->id;
            $HTTP_response_code = $isUpdate ? 200:201;
            $HTTP_response_message = $isUpdate ?'Post has been Edited!':'Post has been Created!';
            if(($isUpdate && $oldId!=$user->id)||!$isUpdate){
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
            else{
                return response()->json([
                    'message' => 'Unauthorized, belongs to other user',
                    'status' => '401'
                ], 401);
            }
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