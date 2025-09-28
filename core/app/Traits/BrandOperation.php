<?php

namespace App\Traits;

use App\Models\Brand;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

trait BrandOperation
{
    public function list()
    {
        $baseQuery = Brand::searchable(['name'])->trashFilter()->orderBy('id', getOrderBy());
        $pageTitle = 'Manage Brand';
        $view      = "admin.brand.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "brand");
        }

        $brands = $baseQuery->paginate(getPaginate());

        return responseManager("brands", $pageTitle, 'success', compact('brands', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'  => 'required|string|max:40|unique:brands,name,' . $id,
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);

        if ($id) {
            $brand   = Brand::where('id', $id)->firstOrFailWithApi('brand');
            $message = "Brand updated successfully";
            $remark  = "brand-updated";
        } else {
            $brand   = new Brand();
            $message = "Brand saved successfully";
            $remark  = "brand-insert";
        }

        if ($request->hasFile('image')) {
            try {
                $old             = $brand->image;
                $brand->image = fileUploader($request->image, getFilePath('brand'), getFileSize('brand'), $old);
            } catch (\Exception $exp) {
                $message = 'Couldn\'t upload your image';
                return responseManager('exception', $message);
            }
        }

        $brand->name = $request->name;
        $brand->save();

        adminActivity($remark, get_class($brand), $brand->id);

        return responseManager("brand", $message, 'success', compact('brand'));
    }

    public function status($id)
    {
        return Brand::changeStatus($id);
    }
}
