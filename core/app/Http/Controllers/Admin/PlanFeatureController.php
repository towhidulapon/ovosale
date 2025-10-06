<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    public function features() {
        $pageTitle = 'Plan Features';
        $planFeatures = PlanFeature::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.features', compact('pageTitle', 'planFeatures'));
    }

    public function saveFeature(Request $request, $id = 0) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($id) {
            $feature = PlanFeature::findOrFail($id);
            $feature->name = $request->name;
            $message = "Feature updated successfully";
        } else {
            $feature = new PlanFeature();
            $feature->name = $request->name;
            $message = "Feature created successfully";
        }
        $feature->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function featureStatus($id) {
        return PlanFeature::changeStatus($id);
    }
}
