<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;

trait DesignationOperation
{
    public function list()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $baseQuery = Designation::whereHas('company', function ($q)use ($userIds) {
            $q->whereIn('user_id', $userIds);
        })->searchable(['name', 'company:name', 'department:name'])->with('company', 'department')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Designation';
        $view      = "Template::user.hrm.designation.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "designation", "A4 landscape");
        }

        $designations = $baseQuery->paginate(getPaginate());
        $companies    = Company::whereHas('departments', function ($q) use ($userIds) {
            $q->whereIn('user_id', $userIds);
        })
            ->with('departments')
            ->active()
            ->get();

        return responseManager("designation", $pageTitle, 'success', compact('designations', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'          => 'required|unique:designations,name,' . $id . ',id,company_id,' . $request->company_id . '|string|max:40',
                'company_id'    => 'required|exists:companies,id',
                'department_id' => 'required|exists:departments,id',
            ],
            [
                'company_id.required'    => 'Please select a company',
                'department_id.required' => 'Please select a department',
            ]
        );

        if ($id) {
            $designation = Designation::where('id', $id)->firstOrFailWithApi('designation');
            $message     = "Designation updated successfully";
            $remark      = "designation-updated";
        } else {
            $designation = new Designation();
            $message     = "Designation saved successfully";
            $remark      = "designation-added";
        }

        $designation->name          = $request->name;
        $designation->company_id    = $request->company_id;
        $designation->department_id = $request->department_id;
        $designation->save();

        // adminActivity($remark, get_class($designation), $designation->id);
        return responseManager("designation", $message, 'success', compact('designation'));
    }

    public function status($id)
    {
        return Designation::changeStatus($id);
    }
}
