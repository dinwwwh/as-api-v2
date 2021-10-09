<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use DB;
use Illuminate\Http\Request;
use Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->_search) {
            $tags = Tag::search($request->_search);
        } else {
            $tags = Tag::orderBy('updated_at', 'desc');
        }

        if ($request->_perPage) {
            $tags =  $tags->paginate($request->_perPage);
        } else {
            $tags = $tags->get();
        }

        return TagResource::withLoad($tags);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateTagRequest $request)
    {
        try {
            DB::beginTransaction();

            $tag = Tag::create([
                'slug' => Str::slug($request->name),
                'name' => $request->name,
                'description' => $request->description,
                'parent_slug' => $request->parent
                    ? Tag::find($request->parent['slug'])?->getRepresentation()?->getKey()
                    : null,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return TagResource::withLoad($tag->refresh());
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        return TagResource::withLoad($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        try {
            DB::beginTransaction();

            if (
                $request->parent &&
                $parentTag = Tag::find($request->parent['slug'])?->getRepresentation()
            ) {
                $tag->update(['parent_slug' => $parentTag->getKey()]);
            }

            if ($request->slug && $request->slug != $tag->getKey()) {
                $oldTag = $tag;
                $tag = Tag::create(array_merge(
                    $oldTag->getAttributes(),
                    $request->only('name', 'slug', 'type', 'description'),
                ));
                $tag->created_at = $oldTag->created_at;
                $tag->save();

                DB::table('taggables')
                    ->where('tag_slug', $oldTag->getKey())
                    ->update([
                        'tag_slug' => $tag->getKey(),
                    ]);

                $oldTag->delete();
            } else {
                $tag->update($request->only('type', 'description'));
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return TagResource::withLoad($tag);
    }

    /**
     * Convert all relations of tag to another tag
     *
     */
    public function migrate(Request $request,  Tag $tag, Tag $migratedTag)
    {
        // Find migrated tag has no parent
        $migratedTag = $migratedTag->getRepresentation();
        if (!$migratedTag) {
            return abort(422, 'Migrated tag is invalid.');
        }

        // Migrated tag must differ tag
        if ($migratedTag->getKey() == $tag->getKey())
            return abort(422, 'Migrated tag is invalid.');

        try {
            DB::beginTransaction();

            // Migrate
            $amount = DB::table('taggables')
                ->where('tag_slug', $tag->getKey())
                ->update([
                    'tag_slug' => $migratedTag->getKey()
                ]);

            // Migrate in future
            if ($request->hasMigrateInFuture) {
                $tag->update([
                    'parent_slug' =>  $migratedTag->getKey(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'message' => 'Migrate successfully',
            'data' => [
                'amount' => $amount,
            ],
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Tag $tag)
    {
        //
    }
}
