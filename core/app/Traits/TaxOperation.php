<?php

namespace App\Traits;

use App\Models\Tax;
use Illuminate\Http\Request;

trait TaxOperation
{
    public function list()
    {
        $baseQuery = Tax::searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Tax';
        $view      = "Template::user.tax.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Tax");
        }

        $taxes = $baseQuery->paginate(getPaginate());
        return responseManager("taxes", $pageTitle, 'success', compact('taxes', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|string|max:255|unique:taxes,name,' . $id,
            'percentage' => 'required|numeric|gt:0',
        ]);

        if ($id) {
            $tax     = Tax::where('id', $id)->firstOrFailWithApi('tax');
            $message = "Tax updated successfully";
            $remark  = "tax-updated";
        } else {
            $tax     = new Tax();
            $message = "Tax saved successfully";
            $remark  = "tax-added";
        }

        $tax->name       = $request->name;
        $tax->percentage = $request->percentage;
        $tax->save();

        adminActivity($remark, get_class($tax), $tax->id);

        return responseManager("tax", $message, 'success', compact('tax'));
    }

    public function status($id)
    {
        return Tax::changeStatus($id);
    }
}
