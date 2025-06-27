<?php

namespace App\Http\Controllers\Backend\ScientificSession;


use App\Http\Controllers\Controller;
use App\Models\Conference\Poll;
use App\Models\Conference\PollAnswer;
use Exception;
use Illuminate\Http\Request;
use Mavinoo\Batch\Batch;


class PollController extends Controller
{
    public function index($society, $conference, $id)
    {
        // dd('da');
        $polls = Poll::whereStatus(1)->where('scientific_session_id', $id)->get();
        return view('backend.schedule-plan.poll.index', compact('id', 'polls', 'conference', 'society'));
    }

    public function create($society, $conference, $id)
    {
        return view('backend.schedule-plan.poll.create', compact('id'));
    }

    public function store(Request $request, $society, $conference)
    {

        try {
            // $request->validate([
            //     'question_text' => 'required|array|min:1',
            //     'question_text.*' => 'required|string|max:255',
            //     'answer_text' => 'required|array|min:1',
            //     'answer_text.*' => 'required|string|max:255',
            // ]);

            foreach ($request->questions as $questionData) {
                $question = Poll::create([
                    'scientific_session_id' => $request->scientific_session_id,
                    'question_text' => $questionData['question_text'],
                ]);

                foreach ($questionData['answers'] as $answer) {
                    $question->answers()->create([
                        'answer_text' => $answer['answer_text'],
                        'is_correct' => isset($answer['is_correct']) ? $answer['is_correct'] : 0,
                    ]);
                }
            }

            return redirect()->route('poll.index', [$society, $conference, $request->scientific_session_id])->with('status', 'Poll Added Successfully');
        } catch (Exception $e) {
            throw $e;
            // dd($e);
        }
    }

    public function edit($society, $conference, $id)
    {
        $poll = Poll::whereId($id)->first();

        return view('backend.schedule-plan.poll.create', compact('poll', 'society', 'conference'));
    }

    public function update(Request $request, $society, $conference, $id, Batch $batch)
    {
        try {
            $req = $request->all();
            $poll = Poll::whereId($id)->first();
            $poll->update($req);

            $studentEducationDetail = [];
            $ids = $request->input('id');

            foreach ($request->answer_text as $key => $answer_text) {
                $studentEducationDetail[] = [
                    'answer_text' => $answer_text,
                    'id' => $ids[$key],
                ];
            }

            $batch->update(new PollAnswer(), $studentEducationDetail, 'id');

            return redirect()->route('poll.index', [$society, $conference, $request->scientific_session_id])->with('status', 'Poll Edited Successfully');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function destroy($society, $conference, $id)
    {
        try {
            $poll = Poll::whereId($id)->first();
            $poll->update([
                'status' => 0
            ]);
            return redirect()->route('poll.index', [$society, $conference, $id])->with('status', 'Poll Deleted Successfully');
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
