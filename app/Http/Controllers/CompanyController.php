<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyCollection;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companies = Company::orderBy('id','desc');
        if($request->has('search') AND !empty($request->search)){
            $companies = $companies->where('name','like','%'.$request->search.'%');
        }
        return new CompanyCollection($companies->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {

        $logo = Storage::disk('public')->put('',$request->file);
        Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'logo' => $logo
        ]);

        return response()->json(['status'=>'success','message'=>'Company created successfullly']);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $logo = $company->logo;
        if($request->has('file') AND !empty($request->file) AND $request->file != 'undefined'){
            if (Storage::disk('public')->exists($logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $logo = Storage::disk('public')->put('',$request->file);
        }

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'logo' => $logo
        ]);

        return response()->json(['status'=>'success','message'=>'Company updated successfullly']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->employees()->delete();
        $company->delete();
        return response()->json(['status'=>'success','message'=>'Company deleted successfullly']);
    }
}
