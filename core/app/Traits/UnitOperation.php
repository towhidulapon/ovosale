<?php

namespace App\Traits;

use App\Models\Unit;
use Illuminate\Http\Request;

trait UnitOperation
{
    public function list()
    {
        $baseQuery = Unit::where('user_id', auth()->id())->searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());
        $pageTitle = 'Manage Unit';
        $view      = "Template::user.unit.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Unit");
        }

        $units = $baseQuery->paginate(getPaginate());
        return responseManager("units", $pageTitle, 'success', compact('units', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|string|max:40|unique:units,name,' . $id,
            'short_name' => 'required|string|max:40|unique:units,short_name,' . $id,
        ]);

        $user = auth()->user();

        if ($id) {
            $unit    = Unit::where('id', $id)->firstOrFailWithApi('unit');
            $message = "Unit updated successfully";
            $remark  = "unit-updated";
        } else {
            $unit    = new Unit();
            $message = "Unit saved successfully";
            $remark  = "unit-updated";
        }

        $unit->user_id    = $user->id;
        $unit->name       = $request->name;
        $unit->short_name = $request->short_name;
        $unit->save();

        // adminActivity($remark, get_class($unit), $unit->id);
        return responseManager("unit", $message, 'success', compact('unit'));
    }

    public function status($id)
    {
        return Unit::changeStatus($id);
    }
}
