@extends('backend.layouts.conference.main')
@section('title')
    {{ isset($poll) ? 'Edit' : 'Add' }} Poll
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a
                    href="{{ route('poll.index', isset($poll) ? [$society, $conference, $poll->scientificSession->id] : [$society, $conference, $id]) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($poll) ? 'Edit' : 'Add' }} Poll</h4>
            <div class="card-body">
                @if (!isset($poll))
                    <form method="POST" action="{{ route('poll.store', [$society, $conference]) }}">
                        @csrf
                        <input type="hidden" name="scientific_session_id" value="{{ $id }}">
                        <div id="questions-container">
                            <div class="question-group border rounded p-3 mb-4">
                                <h6 class="text-secondary mb-3">Question 1</h6>
                                <div class="row">
                                    <div class="mb-3 col-md-8">
                                        <label for="question_text_0" class="form-label">Question Text</label>
                                        <input type="text" class="form-control border-secondary" id="question_text_0"
                                            name="questions[0][question_text]" placeholder="Enter your question..."
                                            required>
                                    </div>
                                </div>
                                <div id="answers-container-0">
                                    <div class="row answer-group">
                                        <div class="mb-3 col-md-8">
                                            <label for="answer_text_0_0" class="form-label">Answer 1</label>
                                            <input type="text" class="form-control border-secondary" id="answer_text_0_0"
                                                name="questions[0][answers][0][answer_text]" placeholder="Enter answer..."
                                                required>

                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-answer mt-2"
                                    data-question="0">
                                    <i class="bi bi-plus-circle"></i> Add Answer
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-success" id="add-question">
                                <i class="bi bi-plus-square"></i> Add Question
                            </button>
                            <button type="submit" class="btn btn-primary ">
                                <i class="bi bi-save"></i> Save Poll
                            </button>
                        </div>
                    </form>
                @else
                    <form method="POST" action="{{ route('poll.update', [$society, $conference, $poll->id]) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="scientific_session_id" value="{{ $poll->scientificSession->id }}">

                        <div id="questions-container">
                            <div class="question-group border rounded p-3 mb-4">
                                <h6 class="text-secondary mb-3">Edit Question</h6>
                                <div class="row">
                                    <div class="mb-3 col-md-8">
                                        <label for="question_text_0" class="form-label">Question Text</label>
                                        <input type="text" class="form-control border-secondary" id="question_text_0"
                                            value="{{ $poll->question_text }}" name="question_text" required>
                                    </div>
                                </div>
                                @foreach ($poll->answers as $answer)
                                    <input type="hidden" name="id[]" value="{{ $answer->id }}">
                                    <div id="answers-container-0">
                                        <div class="row answer-group">
                                            <div class="mb-3 col-md-8">
                                                <label for="answer_text_0_0" class="form-label">Answer</label>
                                                <input type="text" class="form-control border-secondary"
                                                    id="answer_text_0_0" name="answer_text[]"
                                                    value="{{ $answer->answer_text }}" required>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary mt-4">
                                <i class="bi bi-pencil-square"></i> Edit Poll
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            let questionCount = 1;

            $('#add-question').click(function() {
                const newQuestionHtml = `
                    <div class="question-group border rounded p-3 mb-4">
                        <h6 class="text-secondary mb-3">Question ${questionCount + 1}</h6>
                        <div class="row">
                            <div class="mb-3 col-md-8">
                                <label for="question_text_${questionCount}" class="form-label">Question Text</label>
                                <input type="text" class="form-control border-secondary" id="question_text_${questionCount}" name="questions[${questionCount}][question_text]" placeholder="Enter your question..." required>
                            </div>
                        </div>
                        <div id="answers-container-${questionCount}">
                            <div class="row answer-group">
                                <div class="mb-3 col-md-8">
                                    <label for="answer_text_${questionCount}_0" class="form-label">Answer 1</label>
                                    <input type="text" class="form-control border-secondary" id="answer_text_${questionCount}_0" name="questions[${questionCount}][answers][0][answer_text]" placeholder="Enter answer..." required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm add-answer mt-2" data-question="${questionCount}">
                            <i class="bi bi-plus-circle"></i> Add Answer
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-question mt-2">
                            <i class="bi bi-trash"></i> Remove Question
                        </button>
                    </div>
                `;
                $('#questions-container').append(newQuestionHtml);
                questionCount++;
            });

            $(document).on('click', '.add-answer', function() {
                const questionIndex = $(this).data('question');
                const answerCount = $(`#answers-container-${questionIndex} .answer-group`).length;
                const newAnswerHtml = `
                    <div class="row answer-group mt-3">
                        <div class="mb-3 col-md-8">
                            <label for="answer_text_${questionIndex}_${answerCount}" class="form-label">Answer ${answerCount + 1}</label>
                            <input type="text" class="form-control border-secondary" id="answer_text_${questionIndex}_${answerCount}" name="questions[${questionIndex}][answers][${answerCount}][answer_text]" placeholder="Enter answer..." required>
                    
                        </div>
                        <div class="mb-3 col-md-2 mt-7">

                        <button type="button" class="btn btn-outline-danger btn-sm remove-answer ">
                            <i class="icon-base ti tabler-trash me-1 "></i>
                        </button>
                        </div>
                    </div>
                `;
                $(`#answers-container-${questionIndex}`).append(newAnswerHtml);
            });

            $(document).on('click', '.remove-question', function() {
                $(this).closest('.question-group').remove();
            });

            $(document).on('click', '.remove-answer', function() {
                $(this).closest('.answer-group').remove();
            });
        });
    </script>
@endsection
