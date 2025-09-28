<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Employee;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

trait EmployeeOperation
{
    public function list()
    {
        $baseQuery = Employee::searchable(['name', 'email', 'phone', 'company:name', 'department:name', 'designation:name'])->with('company', 'department', 'designation')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Employee';
        $view      = "admin.hrm.employee.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "employee", "A4 landscape");
        }
        $employees    = $baseQuery->paginate(getPaginate());
        $companies    = Company::with(['departments.designations'])->active()->orderBy('name')->get();
        return responseManager("employee", $pageTitle, 'success', compact('employees', 'view', 'pageTitle', 'companies'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'           => 'required|string|max:40',
                'gender'         => 'required|string|max:40',
                'dob'            => 'nullable|max:40',
                'email'          => 'nullable|string|email|max:40|unique:employees,email,' . $id,
                'country'        => 'nullable|string|max:40',
                'phone'          => 'nullable|string|max:40',
                'joining_date'   => 'nullable|max:40',
                'company_id'     => 'required|exists:companies,id',
                'department_id'  => 'required|exists:departments,id',
                'designation_id' => 'required|exists:designations,id',
                'image'          => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
            ],
            [
                'company_id.required'     => 'Please select a company',
                'department_id.required'  => 'Please select a department',
                'designation_id.required' => 'Please select a designation',
            ]
        );

        if ($id) {
            $employee = Employee::where('id', $id)->firstOrFailWithApi('employee');
            $message  = "Employee updated successfully";
            $remark   = "employee-updated";
        } else {
            $employee = new Employee();
            $message  = "Employee saved successfully";
            $remark   = "employee-added";
        }

        if ($request->hasFile('image')) {
            try {
                $old                      = $employee->image;
                $employee->image          = fileUploader($request->image, getFilePath('employeeImage'), getFileSize('employeeImage'), $old);
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your image';
                return responseManager('exception', $message);
            }
        }

        if ($request->hasFile('attachment')) {
            try {
                $employee->attachment = fileUploader($request->attachment, getFilePath('employeeAttachment'));
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your attachment';
                return responseManager('exception', $message);
            }
        }

        $employee->name           = $request->name;
        $employee->gender         = $request->gender;
        $employee->dob            = $request->dob;
        $employee->email          = $request->email;
        $employee->country        = $request->country;
        $employee->phone          = $request->phone;
        $employee->joining_date   = $request->joining_date;
        $employee->company_id     = $request->company_id;
        $employee->department_id  = $request->department_id;
        $employee->designation_id = $request->designation_id;
        $employee->save();

        adminActivity($remark, get_class($employee), $employee->id);
        return responseManager("employee", $message, 'success', compact('employee'));
    }

    public function status($id)
    {
        return Employee::changeStatus($id);
    }
}
