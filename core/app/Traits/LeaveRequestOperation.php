<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait LeaveRequestOperation
{
    public function list()
    {
        $baseQuery = LeaveRequest::searchable(['employee:name','leaveType:name'])->with( 'employee', 'leaveType')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Leave Request';
        $view      = "admin.hrm.leave.request.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "LeaveRequest", "A4 landscape");
        }
        $requests    = $baseQuery->paginate(getPaginate());
        $employees   = Employee::active()->get();
        $types       = LeaveType::active()->get();

        return responseManager("leave_request", $pageTitle, 'success', compact('requests', 'view', 'pageTitle', 'employees', 'types'));
    }


    public function save(Request $request, $id = 0)
    {

        $request->validate(
            [
                'employee_id'   => 'required|exists:employees,id',
                'leave_type_id' => 'required|exists:leave_types,id',
                'start_date'    => 'required|date',
                'end_date'      => 'nullable|date|after_or_equal:start_date',
                'status'        => 'required',
                'reason'        => 'nullable|string',
            ],
            [
                'employee_id.required'   => 'Please select a employee',
                'leave_type_id.required' => 'Please select a leave type',
            ]
        );
        if ($id) {
            $leaveRequest = LeaveRequest::where('id', $id)->firstOrFailWithApi('LeaveRequest');
            $message  = "Leave request updated successfully";
            $remark   = "leave-request-updated";
        } else {
            $leaveRequest = new LeaveRequest();
            $message  = "Leave request saved successfully";
            $remark   = "leave-request-added";
        }
        if ($request->hasFile('attachment')) {
            try {
                $old                      = $leaveRequest->attachment;
                $leaveRequest->attachment = fileUploader($request->attachment, getFilePath('leaveAttachment'), $old);
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your attachment';
                return responseManager('exception', $message);
            }
        }

        //days count
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $days  = $start->diffInDays($end) + 1;

        $leaveRequest->employee_id   = $request->employee_id;
        $leaveRequest->leave_type_id = $request->leave_type_id;
        $leaveRequest->start_date    = $request->start_date;
        $leaveRequest->end_date      = $request->end_date;
        $leaveRequest->days          = intval($days);
        $leaveRequest->status        = $request->status;
        $leaveRequest->reason        = $request->reason;
        $leaveRequest->save();

        adminActivity($remark, get_class($leaveRequest), $leaveRequest->id);
        return responseManager("leave_request", $message, 'success', compact('leaveRequest'));
    }

    public function status($id)
    {
        return LeaveRequest::changeStatus($id);
    }
}
