<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\User;

class Comment extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function toArray($request)
    // {
    //     return json_encode($request);
    // }
    public function toArray($request)
    {
        $collects = $this->collection;
        $arrays = [];
        foreach ($collects as $key => $object) {
            $arrays[$key] = [
                'id' => $object->id,
                'body' => $object->body,
                'user_data' => User::select('id','name','user_image')->findOrFail($object->user_id),
                'commentable_id' => $object->commentable_id,
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at,
            ];
        }
        $reversed = array_reverse($arrays, true);
        return $reversed;
    }
    public function with($request) {
        return [
            'version' => '1.0.0',
            'author_url' => url('http://www.zkabane.com'),
            'type' => 'success'
        ];
    }
}
