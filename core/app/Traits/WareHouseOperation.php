<?php

namespace App\Traits;

use App\Models\Warehouse;
use Illuminate\Http\Request;

trait WareHouseOperation
{

    public $modelName = "Warehouse";

    public function list()
    {
        $baseQuery = Warehouse::where('user_id', auth()->id())->searchable(['name', 'contact_number'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Warehouse';
        $view      = "Template::user.warehouse.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Warehouse", "A4 landscape");
        }

        $warehouses = $baseQuery->paginate(getPaginate());
        return responseManager("warehouse", $pageTitle, 'success', compact('warehouses', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'           => 'required|string|unique:warehouses,name,' . $id,
            'address'        => 'required|string',
            'contact_number' => 'required|string',
            'city'           => 'nullable|string',
            'state'          => 'nullable|string',
            'postcode'       => 'nullable|string',
        ]);

        if ($id) {
            $warehouse = Warehouse::where('id', $id)->firstOrFailWithApi('Warehouse');
            $message   = "Warehouse updated successfully";
            $remark    = "warehouse-updated";
        } else {
            $warehouse = new Warehouse();
            $message   = "Warehouse saved successfully";
            $remark    = "warehouse-added";
        }

        $user = auth()->user();

        $warehouse->user_id        = $user->id;
        $warehouse->contact_number = $request->contact_number;
        $warehouse->name           = $request->name;
        $warehouse->address        = $request->address;
        $warehouse->city           = $request->city;
        $warehouse->state          = $request->state;
        $warehouse->postcode       = $request->postcode;
        $warehouse->save();

        // adminActivity($remark, get_class($warehouse), $warehouse->id);

        return responseManager("warehouse", $message, 'success', compact('warehouse'));
    }

    public function status($id)
    {
        return Warehouse::changeStatus($id);
    }
}
