<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();

        return ['data' => $companies];
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'          => ['required', 'string', 'min:3', 'max:40'],
                'description'   => ['required', 'string', 'min:150', 'max:400'],
                'logo'          => ['nullable', File::types(['png'])->max(3 * 1024)],
            ]);
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $company = new Company();
        $company->name        = $validated['name'];
        $company->description = $validated['description'];

        if (array_key_exists('logo', $validated)) {
            $image_path = 'logos/' . time() . $validated['logo']->getClientOriginalName();
            Storage::put($image_path, $validated['logo']);
            $company->logo = $image_path;
        }

        $request_result = $company->save();
        
        return ['request_result' => $request_result];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:companies,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $company = Company::find($validated['id']);

        return ['data' => $company];
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated_request = $request->validate([
                'name'          => ['required', 'string', 'min:3', 'max:40'],
                'description'   => ['required', 'string', 'min:150', 'max:400'],
                'logo'          => ['nullable', File::types(['png'])->max(3 * 1024)],
            ]);
            $validated_id = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:companies,id']])->validate();
            $validated = array_merge($validated_request, $validated_id);
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $company = Company::find($validated['id']);
        $company->name        = $validated['name'];
        $company->description = $validated['description'];
        //$company->logo = $validated['logo'];
        $company->logo        = 'some address';

        $request_result = $company->save();
        
        return ['request_result' => $request_result];

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $validated = validator(['id' => $id], ['id' => ['required', 'integer', 'exists:companies,id']])->validate();
        } catch (ValidationException $e) {
            return ['error' => $e->getMessage()];
        }

        $company = Company::find($validated['id']);
        $request_result = $company->delete();

        return ['request_result' => $request_result];
    }

    /**
     * Get top of companies based on average rating
     */
    public function getCompaniesTop()
    {
        $top = DB::table('comments')
            ->join('companies', 'comments.company_id', '=', 'companies.id')
            ->select(DB::raw('avg(comments.rating) as average_rating, companies.*'))
            ->groupBy('comments.company_id')
            ->orderByDesc('average_rating')
            ->limit(10)
            ->get();

        return ['data' => $top];
    }
}
