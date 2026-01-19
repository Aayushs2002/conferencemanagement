<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ArticleType;
use App\Models\Conference\ArticleTypeSetting;
use Exception;
use Illuminate\Http\Request;

class ArticleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($society, $conference)
    {
        $articleTypes = ArticleType::where([
            'conference_id' => $conference->id,
            'status' => 1
        ])->orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();

        return view('backend.conference.article-type.index', compact('articleTypes', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        // dd('sa'); 
        return view('backend.conference.article-type.create-modal', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $validated['conference_id'] = $conference->id;
            ArticleType::create($validated);

            return response()->json(['message' => 'Article Type Added Successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, ArticleType $articleType)
    {
        return view('backend.conference.article-type.create-modal', compact('articleType', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, ArticleType $articleType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $articleType->update($validated);

            return response()->json(['message' => 'Article Type Updated Successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, ArticleType $articleType)
    {
        try {
            $articleType->update(['status' => 0]);
            return redirect()->route('articleType.index', [$society, $conference])->with('status', 'Article Type Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('delete', 'Internal Server Error');
        }
    }


    public function updateOrder(Request $request, $society, $conference)
    {
        try {
            $orders = $request->orders;

            foreach ($orders as $order) {
                ArticleType::where('id', $order['id'])->update([
                    'display_order' => $order['position']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the article type settings form.
     */
    public function setting(Request $request, $society, $conference)
    {
        $articleTypeId =  $request->article_type_id;
        $articleType = ArticleType::findOrFail($articleTypeId);
        $setting = ArticleTypeSetting::firstOrNew(['article_type_id' => $articleType->id]);

        return view('backend.conference.article-type.setting-modal', compact('articleType', 'setting', 'society', 'conference'));
    }

    /**
     * Store or update article type settings.
     */
    public function settingSubmit(Request $request, $society, $conference)
    {
        $articleTypeId = $request->article_type_id;

        // Dynamic validation rules based on whether sections exist
        $rules = [
            'total_marks' => 'required|numeric|min:1|max:100',
            'number_of_sections' => 'required|integer|min:0|max:10',
            'attachment_name' => 'nullable|string|max:255',
            'author_limit' => 'nullable|integer|min:1',
        ];

        // If sections exist in the request, validate them
        if ($request->has('section_name') && is_array($request->section_name)) {
            $sectionCount = count($request->section_name);
            for ($i = 0; $i < $sectionCount; $i++) {
                $rules["section_name.{$i}"] = 'required|string|max:255';
                $rules["section_word_limit.{$i}"] = 'required|integer|min:1';
                $rules["section_max_marks.{$i}"] = 'required|numeric|min:0';
                $rules["section_instruction.{$i}"] = 'nullable|string';
            }
        }

        // Custom validation messages
        $messages = [
            'total_marks.required' => 'The total marks field is required.',
            'total_marks.numeric' => 'The total marks must be a number.',
            'total_marks.min' => 'The total marks must be at least 1.',
            'total_marks.max' => 'The total marks may not be greater than 100.',
            'section_name.*.required' => 'The section name field is required.',
            'section_name.*.string' => 'The section name must be a string.',
            'section_name.*.max' => 'The section name may not be greater than 255 characters.',
            'section_word_limit.*.required' => 'The word limit field is required.',
            'section_word_limit.*.integer' => 'The word limit must be a number.',
            'section_word_limit.*.min' => 'The word limit must be at least 1.',
            'section_max_marks.*.required' => 'The maximum marks field is required.',
            'section_max_marks.*.numeric' => 'The maximum marks must be a number.',
            'section_max_marks.*.min' => 'The maximum marks must be at least 0.',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            // Build sections array from submitted data
            $sections = [];
            if ($request->has('section_name') && is_array($request->section_name)) {
                foreach ($request->section_name as $i => $name) {
                    $sections[] = [
                        'name' => $name,
                        'word_limit' => $request->section_word_limit[$i] ?? null,
                        'max_marks' => $request->section_max_marks[$i] ?? 2,
                        'instruction' => $request->section_instruction[$i] ?? '',
                        'reviewer_instruction' => $request->section_reviewer_instruction[$i] ?? '',
                    ];
                }
            }

            // Update number_of_sections based on actual sections count
            $actualSectionCount = count($sections);

            $data = [
                'article_type_id' => $articleTypeId,
                'number_of_sections' => $actualSectionCount,
                'sections' => $sections,
                'total_marks' => $request->total_marks ?? 10,
                'attachment_name' => $request->attachment_name,
                'is_attachment_required' => $request->has('is_attachment_required') ? 1 : 0,
                'author_limit' => $request->author_limit,
                'is_conflict_of_interest_required' => $request->has('is_conflict_of_interest_required') ? 1 : 0,
                'is_source_of_funding_required' => $request->has('is_source_of_funding_required') ? 1 : 0,
            ];

            ArticleTypeSetting::updateOrCreate(
                ['article_type_id' => $articleTypeId],
                $data
            );

            return response()->json(['success' => true, 'message' => 'Article Type Settings Saved Successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
