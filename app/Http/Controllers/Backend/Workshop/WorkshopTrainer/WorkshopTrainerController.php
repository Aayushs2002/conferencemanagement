<?php

namespace App\Http\Controllers\Backend\Workshop\WorkshopTrainer;

use App\Http\Controllers\Controller;
use App\Models\User\Society;
use App\Models\Workshop\Workshop;
use App\Models\Workshop\WorkshopRegistration;
use App\Models\Workshop\WorkshopTrainer;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class WorkshopTrainerController extends Controller
{
    public function __construct(protected FileService $fileService) {}
    public function index($society, $conference, $workshop)
    {

        $trainers = WorkshopRegistration::where(['workshop_id' => $workshop->id, 'registrant_type' => 2, 'status' => 1])->latest()->get();
        return view('backend.workshop.workshop-trainer.index', compact('workshop', 'trainers', 'society', 'conference'));
    }

    public function create($society, $conference, $workshop)
    {
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.workshop.workshop-trainer.create', compact('workshop', 'society', 'conference', 'users'));
    }

    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',
            ];

            $validated = $request->validate($rules);
            $workshop = Workshop::where('id', $validated['workshop_id'])->first();
            $validated['registrant_type'] = 2;
            $validated['token'] = random_word(60);
            $validated['verified_status'] = 1;

            // $validated['image'] = $this->fileService->fileUpload($validated['image'], 'trainer_image', 'workshop/trainers/image');
            // $validated['cv'] = $this->fileService->fileUpload($validated['cv'], 'trainer_cv', 'workshop/trainers/cv');

            WorkshopRegistration::create($validated);

            return redirect()->route('workshop.workshop-trainer.index', [$society, $conference, $workshop])->with('status', 'Trainer Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function edit($society, $conference, $workshop, WorkshopRegistration $trainer)
    {
        $society = Society::with(['users' => function ($query) {
            $query->where('type', 3)->orderByDesc('id');
        }])->where([
            'id' => $conference->society_id,
            'status' => 1
        ])->first();

        $users = $society ? $society->users : collect();
        return view('backend.workshop.workshop-trainer.create', compact('workshop', 'trainer', 'society', 'conference', 'users'));
    }


    public function update(Request $request, $society, $conference, WorkshopRegistration $trainer)
    {
        try {
            $rules = [
                'workshop_id' => 'required',
                'user_id' => 'required',

            ];

            $validated = $request->validate($rules);
            $validated['registrant_type'] = 2;

            $workshop = Workshop::where('id', $validated['workshop_id'])->first();

            // if (!empty($validated['image'])) {
            //     $this->fileService->deleteFile($trainer->image, 'workshop/trainers/image');
            //     $validated['image'] = $this->fileService->fileUpload($validated['image'], 'trainer_image', 'workshop/trainers/image');
            // }

            // if (!empty($validated['cv'])) {
            //     $this->fileService->deleteFile($trainer->cv, 'workshop/trainers/cv');
            //     $validated['cv'] = $this->fileService->fileUpload($validated['cv'], 'trainer_cv', 'workshop/trainers/cv');
            // }

            $trainer->update($validated);

            return redirect()->route('workshop.workshop-trainer.index', [$society, $conference, $workshop])->with('status', 'Trainer Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function destroy(WorkshopRegistration $trainer)
    {
        $trainer->update(['status' => 0]);
        return redirect()->back()->with('delete', 'Trainer Deleted Successfully');
    }
}
