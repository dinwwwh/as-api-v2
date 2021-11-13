<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CreateRequest;
use App\Http\Requests\Comment\IndexRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use DB;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        if ($search = $request->_search) {
            $comments = Comment::search($search);
        } else {
            $comments = Comment::orderBy('id', 'DESC');
        }

        if (
            $commentableId =  $request->commentableId
            && $commentableType =  $request->commentableType
        ) {
            $comments->where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType);
        }

        if ($perPage = $request->_perPage) {
            $comments = $comments->paginate($perPage);
        } else {
            $comments = $comments->get();
        }

        return CommentResource::withLoad($comments);
    }

    /**
     * Handle create a request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateRequest $request)
    {
        $commentable = $request->commentable;

        try {
            DB::beginTransaction();

            $comment = Comment::create([
                'commentable_id' => $commentable->getKey(),
                'commentable_type' => $commentable->getMorphClass(),
                'content' => $request->content,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return CommentResource::withLoad($comment);
    }
}
