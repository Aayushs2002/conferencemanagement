   @extends('backend.layouts.conference.main')
   @section('title')
       Presentation Submission
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
       <div class="col-md">
           <div class="card">
               <h4 class="card-header"><a
                       href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}"><i
                           class="ti tabler-arrow-narrow-left"></i></a>
                   {{ isset($submission) ? 'Edit' : 'Add' }}
                   Presentation Submission</h4>
               <div class="card-body">
                   <form class="needs-validation"
                       action="{{ isset($submission) ? route('my-society.conference.submission.update', [$society, $conference, $submission]) : route('my-society.conference.submission.store',  [$society, $conference]) }}"
                       method="POST" enctype="multipart/form-data" novalidate>
                       @csrf

                       @isset($submission)
                           @method('patch')
                       @endisset
                       <div class="row">
                           <div class="mb-6 col-md-6">
                               <label class="form-label" for="society-name">Title of Abstract<code>*</code></label>
                               <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="society-name" placeholder="Enter Title of Abstract" name="title"
                                   value="{{ old('title') ?? @$submission?->title }}" />
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please enter Title of Abstract.</div>
                               @error('title')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>
                           <div class="mb-6 col-md-6">
                               <label for="article_type" class="form-label">Article Type <code>*</code></label>
                               <select class="form-select" name="article_type" id="article_type" required>
                                   <option value="" hidden>-- Select Article Type --</option>
                                   <option value="1"
                                       @if (isset($submission)) {{ $submission->article_type == '1' ? 'selected' : '' }} @else @selected(old('article_type') == '1') @endif>
                                       Original</option>
                                   <option value="2"
                                       @if (isset($submission)) {{ $submission->article_type == '2' ? 'selected' : '' }} @else @selected(old('article_type') == '2') @endif>
                                       Review</option>
                               </select>
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please select Article Type.</div>
                               @error('article_type')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>
                           <div class="mb-6 col-md-6">
                               <label for="submission_category_major_track_id" class="form-label">Category/Major Track
                                   <code>*</code></label>
                               <select class="form-select" name="submission_category_major_track_id"
                                   id="submission_category_major_track_id" required>
                                   <option value="" hidden>-- Select Category/Major Track --</option>
                                   @foreach ($submissionTracks as $submissionTrack)
                                       <option value="{{ $submissionTrack->id }}"
                                           data-content="{{ $submissionTrack->major_areas }}" @selected(old('submission_category_major_track_id', @$submission->submission_category_major_track_id) == $submissionTrack->id)>
                                           {{ $submissionTrack->title }}</option>
                                   @endforeach
                               </select>
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please select Category/Major Track.</div>
                               @error('submission_category_major_track_id')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                               <p id="majorAreas" class="text-success">test</p>
                           </div>

                           <div class="mb-6 col-md-6">
                               <label for="presentation_type" class="form-label">Presentation Type <code>*</code></label>
                               <select class="form-select" name="presentation_type" id="presentation_type" required>
                                   <option hidden>-- Select Presentation Type --</option>
                                   <option value="1"
                                       @if (isset($submission)) {{ $submission->presentation_type == '2' ? 'selected' : '' }} @else @selected(old('presentation_type') == '2') @endif>
                                       Oral</option>
                                   <option value="2"
                                       @if (isset($submission)) {{ $submission->presentation_type == '1' ? 'selected' : '' }} @else @selected(old('presentation_type') == '1') @endif>
                                       Poster</option>
                               </select>
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please select Presentation Type.</div>
                               @error('presentation_type')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>

                           <div class="mb-6 col-md-9">
                               <label for="keyWord" class="form-label">Keywords <code>*(NOTE: Total number of Keywords
                                       limitation is
                                       {{ @$setting->key_word_limit ? @$setting->key_word_limit : 'infinity' }})
                                       <span class="text-info">(Press enter after typing complete word/words to represent it
                                           as a keyword.)</span></code></label>

                               @php
                                   $keywordsJson =
                                       old('keywords') ?:
                                       collect(explode(',', @$submission->keywords))
                                           ->map(fn($kw) => ['value' => $kw])
                                           ->toJson();
                               @endphp

                               <input id="keyWord" class="form-control" name="keywords" required
                                   placeholder="Enter Keywords" value='{{ $keywordsJson }}' />




                               @error('email')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>
                           <div class="col-md-12 form-group mb-3">
                               <label for="abstract_content" class="form-label">Abstract Content <code><span
                                           id="abstractRequired">*
                                       </span><span>(NOTE: Total number of Abstract Words limitation is
                                           {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</span></code></label>
                               <textarea class="form-control" name="abstract_content" id="description2" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content }}</textarea>
                               @error('abstract_content')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>

                           <div class="mb-6 col-md-6">
                               <label class="form-label" for="image">Image/Diagram <code>(optional)</code></label>
                               <input type="file" class="form-control" name="image" id="image"
                                   value="{{ !empty(old('image')) ? old('image') : @$submission->image }}" />
                               <div class="row" id="imgPreview">
                                   @if (isset($submission))
                                       <div class="col-3 mt-2">
                                           <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}"
                                               target="_blank"><img
                                                   src="{{ asset('storage/participant/submission/image/' . $submission->image) }}"
                                                   class="img-fluid" alt="image"></a>
                                       </div>
                                   @endif
                               </div>
                               @error('image')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>


                           <div class="row">
                               <div class="col-12 text-end">
                                   <button type="submit"
                                       class="btn btn-primary">{{ isset($society) ? 'Update' : 'Submit' }}</button>
                               </div>
                           </div>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   @endsection
   @section('scripts')
       <script>
           $(document).ready(function() {

               $('#submission_category_major_track_id').on('change', function() {
                   // Get the selected option's data-content
                   var selectedContent = $(this).find('option:selected').data('content');

                   // Display content if an option is selected
                   if ($(this).val() !== '') {
                       $('#majorAreas').text('(' + selectedContent + ')');
                   } else {
                       $('#majorAreas').text(''); // Clear if no selection
                   }
               });
               // Trigger change event on page load to show content (for edit pages)
               $('#submission_category_major_track_id').trigger('change');

               CKEDITOR.replace('description2', {
                   filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                   filebrowserUploadMethod: "form",
                   extraPlugins: 'wordcount',
                   wordcount: {
                       showWordCount: true,
                       maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
                   }
               });
           });

           const keywordInput = document.querySelector('#keyWord');
           if (keywordInput) {
               new Tagify(keywordInput, {
                   maxTags: {{ @$setting->key_word_limit ?? 9999 }}
               });
           }
       </script>
   @endsection
