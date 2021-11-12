<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTagRequest;
use App\Http\Requests\IndexTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TagResource;
use App\Models\Account;
use App\Models\Tag;
use DB;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexTagRequest $request)
    {
        if ($request->_search) {
            $tags = Tag::search($request->_search);
        } else {
            $tags = Tag::orderBy('updated_at', 'desc');
        }

        if ($request->has('type')) {
            $tags->where('type', $request->type);
        }

        if ($request->_perPage) {
            $tags =  $tags->paginate($request->_perPage);
        } else {
            $tags = $tags->get();
        }

        return TagResource::withLoad($tags);
    }

    /**
     * Get accounts by tag
     *
     */
    public function getSellingAccountsByTag(Tag $tag)
    {
        $accountTypeIds = $tag->accountTypes->pluck('id')->toArray();

        if (request('_search')) {
            $accounts = Account::search(request('_search'));
        } else {
            $accounts = Account::orderBy('id', 'desc');
        }

        $accounts->whereIn('account_type_id', $accountTypeIds);
        $accounts->where('status', Account::SELLING_STATUS);

        if (request('_perPage')) {
            $accounts =  $accounts->paginate(request('_perPage'));
        } else {
            $accounts = $accounts->get();
        }

        return AccountResource::withLoad($accounts);
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
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'parent_slug' => $request->parent
                    ? Tag::find($request->parent['slug'])?->getRepresentation()?->getKey()
                    : null,
                'main_image_path' => $request->hasFile('mainImage')
                    ? $request->mainImage->store('tag-images', 'public')
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

            $data = $request->only('name', 'type', 'description');

            if (
                $request->parent &&
                $parentTag = Tag::find($request->parent['slug'])?->getRepresentation()
            ) {
                $data['parent_slug'] = $parentTag->getKey();
            }

            if ($request->hasFile('mainImage')) {
                $data['main_image_path'] = $request->mainImage->store('tag-images', 'public');
            }

            $tag->update($data);

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
