<?php

namespace App\Http\Controllers;

use App\Http\Requests\Validation\ApprovableRequest;
use App\Http\Requests\Validation\EndApprovingRequest;
use App\Http\Requests\Validation\IndexRequest;
use App\Http\Requests\Validation\StartApprovingRequest;
use App\Http\Resources\ValidationResource;
use App\Models\Validation;
use App\Models\Validator;
use Arr;
use DB;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    /**
     * Get validations
     *
     */
    public function index(IndexRequest $request)
    {
        if ($search = $request->_search) {
            $validations = Validation::search($search);
        } else {
            $validations = Validation::orderBy('id', 'desc');
        }

        if ($request->has('_isApproving')) {
            $validations = $validations->where('is_approving', $request->_isApproving);
        }

        if ($request->has('_isError')) {
            $validations = $validations->where('is_error', $request->_isError);
        }

        if ($request->has('_approverId')) {
            $validations = $validations->where('approver_id', $request->_approverId);
        }


        if ($perPage = $request->_perPage) {
            return ValidationResource::withLoad($validations->paginate($perPage));
        }
        return ValidationResource::withLoad($validations->get());
    }

    public function approvableByMe(ApprovableRequest $request)
    {
        $approvableValidatorIds = Validator::whereRelation('users', 'id', auth()->user()->getKey())
            ->get('id')
            ->pluck('id')
            ->toArray();

        if ($search = $request->_search) {
            $approvableValidations = Validation::search($search);
        } else {
            $approvableValidations = Validation::orderBy('id', 'desc');
        }

        $approvableValidations = $approvableValidations
            ->whereIn('validator_id', $approvableValidatorIds)
            ->where('is_error', false)
            ->whereIn('approver_id', [null, auth()->user()->getKey()]);

        if ($request->has('_isApproving')) {
            $approvableValidations = $approvableValidations->where('is_approving', $request->_isApproving);
        }

        if ($perPage = $request->_perPage) {
            return ValidationResource::withLoad($approvableValidations->paginate($perPage));
        }
        return ValidationResource::withLoad($approvableValidations->get());
    }

    /**
     * Get resource of validation
     *
     */
    public function show(Validation $validation)
    {
        return ValidationResource::withLoad($validation);
    }

    /**
     * Start approving a validation
     *
     */
    public function startApproving(StartApprovingRequest $request, Validation $validation)
    {
        try {
            DB::beginTransaction();

            $validation->update([
                'approver_id' => auth()->user()->getKey(),
                'is_approving' => true,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return ValidationResource::withLoad($validation);
    }

    /**
     * End approving a validation
     *
     */
    public function endApproving(EndApprovingRequest $request, Validation $validation)
    {
        try {
            DB::beginTransaction();

            $data = Arr::snake($request->only([
                'isError',
                'description',
                'updatedValues'
            ]));
            $data['approver_id'] = auth()->user()->getKey();
            $data['is_approving'] = false;
            $validation->update($data);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return ValidationResource::withLoad($validation);
    }
}
