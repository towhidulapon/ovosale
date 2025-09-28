<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Shift;
use Illuminate\Http\Request;

trait ShiftOperation
{
    public function list()
    {
        $baseQuery = Shift::searchable(['name', 'company:name'])->with('company')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Shift';
        $view      = "admin.hrm.shift.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Shift", "A4 landscape");
        }
        $shifts = $baseQuery->paginate(getPaginate());
        $companies   = Company::active()->get();
        return responseManager("shift", $pageTitle, 'success', compact('shifts', 'view', 'pageTitle', 'companies'));
    }


    public function save(Request $request, $id = 0)
    {
        $request->validate(
            [
                'name'                => 'required|string|max:255',
                'company_id'          => 'required|exists:companies,id',
            ],
            [
                'company_id.required' => 'Please select the company',
            ]
        );

        if ($id) {
            $shift    = Shift::where('id', $id)->firstOrFailWithApi('shift');
            $message  = "Shift updated successfully";
            $remark   = "shift-updated";
        } else {
            $shift    = new Shift();
            $message  = "Shift saved successfully";
            $remark   = "shift-added";
        }
        $shift->name          = $request->name;
        $shift->company_id    = $request->company_id;
        $shift->save();

        adminActivity($remark, get_class($shift), $shift->id);
        return responseManager("shift", $message, 'success', compact('shift'));
    }

    public function status($id)
    {
        return Shift::changeStatus($id);
    }
}
