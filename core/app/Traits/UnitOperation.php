<?php

namespace App\Traits;

use App\Models\Unit;
use Illuminate\Http\Request;

trait UnitOperation
{
    public function list()
    {
        $baseQuery = Unit::searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());
        $pageTitle = 'Manage Unit';
        $view      = "admin.unit.list";

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

        if ($id) {
            $unit    = Unit::where('id', $id)->firstOrFailWithApi('unit');
            $message = "Unit updated successfully";
            $remark  = "unit-updated";
        } else {
            $unit    = new Unit();
            $message = "Unit saved successfully";
            $remark  = "unit-updated";
        }

        $unit->name       = $request->name;
        $unit->short_name = $request->short_name;
        $unit->save();

        adminActivity($remark, get_class($unit), $unit->id);
        return responseManager("unit", $message, 'success', compact('unit'));
    }

    public function status($id)
    {
        return Unit::changeStatus($id);
    }
}
