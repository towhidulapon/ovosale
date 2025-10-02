<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

trait DepartmentOperation
{
    public function list()
    {
        $baseQuery = Department::where('user_id', auth()->id())->searchable(['name', 'company:name'])->with('company')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Department';
        $view      = "Template::user.hrm.department.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Department", "A4 landscape");
        }
        $departments = $baseQuery->paginate(getPaginate());
        $companies   = Company::active()->orderBy('name')->get();
        return responseManager("department", $pageTitle, 'success', compact('departments', 'view', 'pageTitle','companies'));
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

        $department->user_id    = auth()->id();
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
