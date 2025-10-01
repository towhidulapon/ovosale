<?php

namespace App\Traits;

use App\Models\LeaveType;
use Illuminate\Http\Request;

trait LeaveTypeOperation
{
    public function typeList()
    {
        $baseQuery = LeaveType::searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Leave Type';
        $view      = "Template::user.hrm.leave.type.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "LeaveType", "A4 landscape");
        }
        $types = $baseQuery->paginate(getPaginate());
        return responseManager("leave_type", $pageTitle, 'success', compact('view', 'pageTitle','types'));
    }


    public function typeSave(Request $request, $id = 0)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
        ]);
        if ($id) {
            $leaveType = LeaveType::where('id', $id)->firstOrFailWithApi('LeaveType');
            $message   = "Leave type updated successfully";
            $remark    = "leave-type-updated";
        } else {
            $leaveType = new LeaveType();
            $message   = "Leave type saved successfully";
            $remark    = "leave-type-added";
        }
        $leaveType->name     = $request->name;
        $leaveType->save();

        adminActivity($remark, get_class($leaveType), $leaveType->id);
        return responseManager("leave_type", $message, 'success', compact('leaveType'));
    }

    public function typeStatus($id)
    {
        return LeaveType::changeStatus($id);
    }
}
