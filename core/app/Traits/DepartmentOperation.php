<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

trait DepartmentOperation
{
    public function list()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $baseQuery = Department::whereHas('company', function ($q) use ($userIds) {
            $q->where('user_id', $userIds);
        })->searchable(['name', 'company:name'])->with('company')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Department';
        $view      = "Template::user.hrm.department.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Department", "A4 landscape");
        }
        $departments = $baseQuery->paginate(getPaginate());
        $companies   = Company::active()->orderBy('name')->get();
        return responseManager("department", $pageTitle, 'success', compact('departments', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|unique:departments,name,' . $id . ',id,company_id,' . $request->company_id . '|string|max:40',
            'company_id' => 'required|exists:companies,id',
        ],
            [
                'company_id.required' => 'Please select the company',
            ]);

        if ($id) {
            $department = Department::where('id', $id)->firstOrFailWithApi('department');
            $message    = "Department updated successfully";
            $remark     = "department-updated";
        } else {
            $department = new Department();
            $message    = "Department saved successfully";
            $remark     = "department-added";
        }

        $department->name       = $request->name;
        $department->company_id = $request->company_id;
        $department->save();

        // adminActivity($remark, get_class($department), $department->id);
        return responseManager("department", $message, 'success', compact('department'));
    }

    public function status($id)
    {
        return Department::changeStatus($id);
    }
}
