<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\UserDepartment;
use App\Models\User\UserDesignation;
use App\Models\User\UserDetail;
use App\Models\User\UserInstitution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtherDataApprovalController extends Controller
{
    public function index()
    {
        return view('backend.approvals.other-data', [
            'otherInstitutions' => UserInstitution::with('user')->get(),
            'otherDesignations' => UserDesignation::with('user')->get(),
            'otherDepartments' => UserDepartment::with('user')->get(),
            'institutions' => Institution::all(),
            'designations' => Designation::all(),
            'departments' => Department::all(),
        ]);
    }

    public function approve(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:institution,designation,department',
            'id' => 'required|integer',
            'target_id' => 'nullable|integer',
        ]);

        $type = $validated['type'];
        $targetId = $validated['target_id'];

        DB::transaction(function () use ($type, $validated, $targetId) {
            switch ($type) {
                case 'institution':
                    $otherModel = UserInstitution::class;
                    $mainModel = Institution::class;
                    $field = 'institution_id';
                    $mainNameField = 'name';
                    $otherNameField = 'institution_name';
                    break;

                case 'designation':
                    $otherModel = UserDesignation::class;
                    $mainModel = Designation::class;
                    $field = 'designation_id';
                    $mainNameField = 'designation';
                    $otherNameField = 'designation_name';
                    break;

                case 'department':
                    $otherModel = UserDepartment::class;
                    $mainModel = Department::class;
                    $field = 'department_id';
                    $mainNameField = 'name';
                    $otherNameField = 'department_name';
                    break;
            }

            $other = $otherModel::findOrFail($validated['id']);

            // Create new main record if not merging into existing
            if (!$targetId) {
                $main = $mainModel::create([
                    $mainNameField => $other->$otherNameField,
                ]);
                $targetId = $main->id;
            }

            // Update user_detail with the correct ID
            UserDetail::where('user_id', $other->user_id)->update([
                $field => $targetId
            ]);

            // Delete the "other" record
            $other->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Data approved and merged successfully.'
        ]);
    }

    public function reject(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:institution,designation,department',
            'id' => 'required|integer',
        ]);

        $model = match ($validated['type']) {
            'institution' => UserInstitution::class,
            'designation' => UserDesignation::class,
            'department' => UserDepartment::class,
        };

        $model::findOrFail($validated['id'])->delete();

        return response()->json(['success' => true, 'message' => 'Entry rejected and removed.']);
    }
}
