<?php

namespace App\Traits;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Http\Request;

trait AttributeOperation
{
    public function list()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if (!in_array($user->id, $userIds)) {
            $userIds[] = $user->id;
        }
        $baseQuery = Attribute::whereIn('user_id', $userIds)->searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());

        $pageTitle = 'Manage Attribute';
        $view      = "Template::user.attribute.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "Attribute");
        }
        $attributes = $baseQuery->get();
        return responseManager("attributes", $pageTitle, 'success', compact('attributes', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $id,
        ]);

        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if ($id) {
            $attribute = Attribute::where('id', $id)->whereIn('user_id', $userIds)->firstOrFailWithApi('attribute');
            $message = "Attribute updated successfully";
            $remark  = "attribute-updated";
        } else {
            $attribute          = new Attribute();
            $message            = "Attribute saved successfully";
            $remark             = "attribute-updated";
            $attribute->user_id = $user->id;
        }

        $attribute->name = $request->name;
        $attribute->save();
        // adminActivity($remark, get_class($attribute), $attribute->id);
        return responseManager("attribute", $message, 'success', compact('attribute'));
    }

    public function status($id)
    {
        return Attribute::changeStatus($id);
    }
}
