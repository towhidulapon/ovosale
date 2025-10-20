<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

trait CompanyOperation
{
    public function list()
    {
        $user = getPArentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if (!in_array($user->id, $userIds)) {
            $userIds[] = $user->id;
        }

        $baseQuery = Company::whereIn('user_id', $userIds)->searchable(['name', 'email', 'mobile'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Company';
        $view      = "Template::user.hrm.company.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Company", "A4 landscape");
        }
        $companies = $baseQuery->paginate(getPaginate());

        return responseManager("company", $pageTitle, 'success', compact('companies', 'view', 'pageTitle'));
    }


    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'    => 'required|Unique:companies,name,' . $id . ',id|string|max:40',
            'email'   => 'nullable|string|email|max:40|unique:companies,email,' . $id,
            'mobile'  => 'nullable|string|max:40|unique:companies,mobile,' . $id,
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if ($id) {
            $company = Company::where('id', $id)->firstOrFailWithApi('company');
            $message  = "Company updated successfully";
            $remark   = "company-updated";
        } else {
            $company = new Company();
            $message  = "Company saved successfully";
            $remark   = "company-added";
            $company->user_id = auth()->id();
        }

        $company->name    = $request->name;
        $company->email   = $request->email;
        $company->mobile  = $request->mobile;
        $company->country = $request->country;
        $company->address = $request->address;
        $company->save();

        // adminActivity($remark, get_class($company), $company->id);

        return responseManager("company", $message, 'success', compact('company'));
    }

    public function status($id)
    {
        return Company::changeStatus($id);
    }
}
