<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = new Company;
        if($request->has('search') AND !empty($request->search)){
            $companies = $companies->where('name','like','%'.$request->search.'%');
        }
        return $companies->orderBy('id','desc')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function users()
    {
        return Company::orderBy('id','desc')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'logo' => $request->website
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'logo' => $request->website
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Company created successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
