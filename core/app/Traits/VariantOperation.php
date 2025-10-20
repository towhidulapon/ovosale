<?php

namespace App\Traits;

use App\Models\Attribute;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Http\Request;

trait VariantOperation
{
    public function list()
    {

        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if (!in_array($user->id, $userIds)) {
            $userIds[] = $user->id;
        }

        $baseQuery = Variant::with('attribute')->whereIn('user_id', $userIds)->searchable(['name', 'attribute:name'])->orderBy('id', getOrderBy())->trashFilter();

        $pageTitle = 'Manage Variant';
        $view      = "Template::user.variant.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Variant");
        }

        $variants   = $baseQuery->paginate(getPaginate());
        $attributes = Attribute::active()->get();

        return responseManager("variants", $pageTitle, 'success', compact('variants', 'view', 'pageTitle', 'attributes'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'      => 'required|string|max:40',
            'attribute' => 'required|integer|exists:attributes,id',
        ]);

        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if ($id) {
            $variant = Variant::where('id', $id)->firstOrFailWithApi('variant');
            $exists  = Variant::where('attribute_id', $request->attribute)->where('name', $request->name)->where('id', '!=', $id)->exists();
        } else {
            $exists = Variant::where('attribute_id', $request->attribute)->where('name', $request->name)->exists();
        }

        if ($exists) {
            $message = "This variant already exists for this attribute. Please choose a different one.";
            return responseManager("already_exists", $message);
        }

        if ($id) {
            $variant = Variant::where('id', $id)->whereIn('user_id', $userIds)->firstOrFailWithApi('variant');
            $message = "Variant updated successfully";
            $remark  = "variant-updated";
        } else {
            $variant          = new Variant();
            $message          = "Variant saved successfully";
            $remark           = "variant-updated";
            $variant->user_id = $user->id;
        }

        $variant->name         = $request->name;
        $variant->attribute_id = $request->attribute;
        $variant->save();

        // adminActivity($remark, get_class($variant), $variant->id);
        return responseManager("variant", $message, 'success', compact('variant'));
    }

    public function status($id)
    {
        return Variant::changeStatus($id);
    }
}
