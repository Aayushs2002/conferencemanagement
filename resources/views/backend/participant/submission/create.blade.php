   @extends('backend.layouts.conference.main')
   @section('title')
       Presentation Submission
   @endsection
   @section('content')
       @include('backend.layouts.conference-navigation')
       @if ($setting?->abstract_guidelines)
           <div class="modal fade" id="openAbstractGuidelineModal" tabindex="-1" role="dialog"
               aria-labelledby="exampleModalCenterTitleDuideline" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-simple modal-pricing">
                   <div class="modal-content" id="modalContent">
                       <div class="modal-body">
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           <h4 class="text-center mb-4">Abstract Submission Guidelines</h4>
                           {!! $setting->abstract_guidelines !!}
                       </div>
                   </div>
               </div>
           </div>
       @endif
       <div class="col-md">
           <div class="card">
               <h4 class="card-header"><a
                       href="{{ route('my-society.conference.submission.index', [$society, $conference]) }}"><i
                           class="ti tabler-arrow-narrow-left"></i></a>
                   {{ isset($submission) ? 'Edit' : 'Add' }}
                   Presentation Submission</h4>
               <div class="card-body">
                   <form class="needs-validation"
                       action="{{ isset($submission) ? route('my-society.conference.submission.update', [$society, $conference, $submission]) : route('my-society.conference.submission.store', [$society, $conference]) }}"
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
                                   value="{{ old('title') ?? @$submission?->title }}" required />
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please enter Title of Abstract.</div>
                               @error('title')
                                   <p class="text-danger">{{ $message }}</p>
                               @enderror
                           </div>
                           <div class="mb-6 col-md-6">
                               <label for="article_type_id" class="form-label">Article Type <code>*</code></label>
                               <select class="form-select" name="article_type_id" id="article_type_id" required>
                                   <option value="" hidden>-- Select Article Type --</option>
                                   @foreach ($articleTypes as $articleType)
                                       <option value="{{ $articleType->id }}" @selected(old('article_type_id', @$submission->article_type_id) == $articleType->id)>
                                           {{ $articleType->name }}
                                       </option>
                                   @endforeach
                               </select>
                               <div class="valid-feedback">Looks good!</div>
                               <div class="invalid-feedback">Please select Article Type.</div>
                               @error('article_type_id')
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
                                   <option value="" hidden>-- Select Presentation Type --</option>
                                   <option value="1"
                                       @if (isset($submission)) {{ $submission->presentation_type == '1' ? 'selected' : '' }} @else @selected(old('presentation_type') == '1') @endif>
                                       Poster</option>
                                   <option value="2"
                                       @if (isset($submission)) {{ $submission->presentation_type == '2' ? 'selected' : '' }} @else @selected(old('presentation_type') == '2') @endif>
                                       Oral</option>
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

                           <!-- Dynamic Content Sections Container -->
                           <div id="contentSectionsContainer">
                               <!-- Default Abstract Content (shown when no article type selected or no settings) -->
                               <div class="col-md-12 form-group mb-3" id="defaultAbstractContent" style="display: none;">
                                   <label for="abstract_content" class="form-label">Abstract Content <code><span
                                               id="abstractRequired">*
                                           </span><span>(NOTE: Total number of Abstract Words limitation is
                                               {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</span></code></label>
                                   <textarea class="form-control" name="abstract_content" id="description2" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content }}</textarea>
                                   @error('abstract_content')
                                       <p class="text-danger">{{ $message }}</p>
                                   @enderror
                               </div>
                           </div>

                           <!-- Dynamic Attachment Field Container -->
                           <div id="attachmentContainer">
                               @if ($setting->attachment_name)
                                   <div class="mb-6 col-md-6" id="defaultAttachment" style="display: none;">
                                       <label class="form-label" for="image">{{ $setting->attachment_name }}
                                           <code>{{ $setting->attachment_required == true ? '*' : '(optional)' }}</code></label>
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
                               @endif
                           </div>

                           <!-- Conflict of Interest Field Container -->
                           <div id="conflictOfInterestContainer"></div>

                           <!-- Source of Funding Field Container -->
                           <div id="sourceOfFundingContainer"></div>
                           @if (!isset($submission))
                               <div class="mb-6 col-md-6">
                                   <label class="form-label">Are you the Main Author? <code>*</code></label>
                                   <div class="mt-2">
                                       <div class="form-check form-check-inline">
                                           <input class="form-check-input" type="radio" name="main_author"
                                               id="main_author_yes" value="1" @checked(old('main_author') == '1') required>
                                           <label class="form-check-label" for="main_author_yes">Yes</label>
                                       </div>
                                       <div class="form-check form-check-inline">
                                           <input class="form-check-input" type="radio" name="main_author"
                                               id="main_author_no" value="0" @checked(old('main_author') == '0') required>
                                           <label class="form-check-label" for="main_author_no">No</label>
                                       </div>
                                   </div>
                                   <div class="invalid-feedback d-block">Please select if you are the main author.</div>
                                   @error('main_author')
                                       <p class="text-danger">{{ $message }}</p>
                                   @enderror
                               </div>
                           @endif
                           <div class="row">
                               <div class="col-12 text-end">
                                   <button type="submit"
                                       class="btn btn-primary">{{ isset($submission) ? 'Update' : 'Submit' }}</button>
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
           let articleTypeSettings = {};
           let ckeditorInstances = {};

           $(document).ready(function() {
               $('#openAbstractGuidelineModal').modal('show');


               $('#submission_category_major_track_id').on('change', function() {
                   var selectedContent = $(this).find('option:selected').data('content');
                   if ($(this).val() !== '') {
                       $('#majorAreas').text('(' + selectedContent + ')');
                   } else {
                       $('#majorAreas').text('');
                   }
               });
               $('#submission_category_major_track_id').trigger('change');

               // Load article type settings when article type changes
               $('#article_type_id').on('change', function() {
                   const articleTypeId = $(this).val();
                   if (!articleTypeId) {
                       showDefaultFields();
                       return;
                   }

                   loadArticleTypeSettings(articleTypeId);
               });

               // Trigger on page load if editing or if validation failed (old data exists)
               @if (isset($submission) && $submission->article_type_id)
                   $('#article_type_id').trigger('change');
               @elseif (old('article_type_id'))
                   $('#article_type_id').trigger('change');
               @endif
           });

           function loadArticleTypeSettings(articleTypeId) {
               $.ajax({
                   url: '{{ route('my-society.conference.submission.get-article-type-setting', [$society, $conference]) }}',
                   type: 'GET',
                   data: {
                       article_type_id: articleTypeId
                   },
                   success: function(response) {
                       if (response.has_setting) {
                           articleTypeSettings = response.setting;
                           showDynamicFields(response.setting);
                       } else {
                           showDefaultFields();
                       }
                   },
                   error: function() {
                       showDefaultFields();
                   }
               });
           }

           function showDynamicFields(setting) {
               // Destroy existing CKEditor instances
               for (let key in ckeditorInstances) {
                   if (ckeditorInstances[key]) {
                       ckeditorInstances[key].destroy();
                   }
               }
               ckeditorInstances = {};

               // Clear containers
               $('#contentSectionsContainer').empty();
               $('#attachmentContainer').empty();
               $('#conflictOfInterestContainer').empty();
               $('#sourceOfFundingContainer').empty();

               if (setting.number_of_sections > 0 && setting.sections) {
                   // Show dynamic sections
                   setting.sections.forEach((section, index) => {
                       const sectionName = section.name || 'Section ' + (index + 1);
                       const wordLimit = section.word_limit || '';
                       const instruction = section.instruction || '';

                       // Get old value for this section if validation failed, otherwise use existing submission data
                       let oldSectionContent = '';
                       @if (old('sections'))
                           oldSectionContent = @json(old('sections'))[index]?.content || '';
                       @elseif (isset($submission) && $submission->sections)
                           const submissionSections = @json($submission->sections);
                           oldSectionContent = submissionSections[index]?.content || '';
                       @endif

                       // Get error message for this section if exists
                       let sectionError = '';
                       @if ($errors->any())
                           const sectionErrors = @json($errors->messages());
                           if (sectionErrors[`sections.${index}.content`]) {
                               sectionError =
                                   `<p class="text-danger">${sectionErrors[`sections.${index}.content`][0]}</p>`;
                           }
                       @endif

                       const sectionHtml = `
                           <div class="col-md-12 form-group mb-3">
                               <label for="section_${index}" class="form-label">${sectionName} <code>*${wordLimit ? ` (Word Limit: ${wordLimit})` : ''}</code></label>
                               ${instruction ? `<p class="text-muted small mb-2"><i class="ti tabler-info-circle"></i> ${instruction}</p>` : ''}
                               <textarea class="form-control section-content ${sectionError ? 'is-invalid' : ''}" name="sections[${index}][content]" id="section_${index}" cols="30" rows="5"></textarea>
                               <input type="hidden" name="sections[${index}][name]" value="${sectionName}">
                               <input type="hidden" name="sections[${index}][word_limit]" value="${wordLimit}">
                               ${sectionError}
                           </div>
                       `;
                       $('#contentSectionsContainer').append(sectionHtml);

                       // Initialize CKEditor for this section
                       ckeditorInstances[`section_${index}`] = CKEDITOR.replace(`section_${index}`, {
                           filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                           filebrowserUploadMethod: "form",
                           extraPlugins: 'wordcount',
                           wordcount: {
                               showWordCount: true,
                               maxWordCount: wordLimit || Infinity,
                           }
                       });

                       // Set old content after CKEditor is ready
                       if (oldSectionContent) {
                           ckeditorInstances[`section_${index}`].on('instanceReady', function() {
                               this.setData(oldSectionContent);
                           });
                       }
                   });
               } else {
                   // Show default abstract content with error handling
                   const abstractError =
                       '@error('abstract_content')<p class="text-danger">{{ $message }}</p>@enderror';
                   const abstractHtml = `
                       <div class="col-md-12 form-group mb-3">
                           <label for="abstract_content" class="form-label">Abstract Content <code>*
                               (Word Limit: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</code></label>
                           <textarea class="form-control @error('abstract_content') is-invalid @enderror" name="abstract_content" id="description2" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content }}</textarea>
                           ${abstractError}
                       </div>
                   `;
                   $('#contentSectionsContainer').html(abstractHtml);

                   ckeditorInstances['description2'] = CKEDITOR.replace('description2', {
                       filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                       filebrowserUploadMethod: "form",
                       extraPlugins: 'wordcount',
                       wordcount: {
                           showWordCount: true,
                           maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
                       }
                   });
               }

               // Handle attachment
               if (setting.is_attachment_required && setting.attachment_name) {
                   const oldImage = '{{ old('image') }}';
                   const submissionImage = '{{ @$submission->image }}';
                   let imagePreview = '';

                   @if (isset($submission) && $submission->image)
                       imagePreview = `
                           <div class="row mt-2" id="imgPreview">
                               <div class="col-3">
                                   <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}" target="_blank">
                                       <img src="{{ asset('storage/participant/submission/image/' . $submission->image) }}" class="img-fluid" alt="image">
                                   </a>
                               </div>
                           </div>
                       `;
                   @endif

                   const imageError =
                       '@error('image')<p class="text-danger">{{ $message }}</p>@enderror';
                   const attachmentHtml = `
                       <div class="mb-6 col-md-6">
                           <label class="form-label" for="image">${setting.attachment_name} <code>*</code></label>
                           <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" ${submissionImage ? '' : 'required'} />
                           ${oldImage ? `<small class="text-muted">Previous file: ${oldImage}</small>` : ''}
                           ${imagePreview}
                           ${imageError}
                       </div>
                   `;
                   $('#attachmentContainer').html(attachmentHtml);
               } else if (!setting.is_attachment_required && setting.attachment_name) {
                   const oldImage = '{{ old('image') }}';
                   const submissionImage = '{{ @$submission->image }}';
                   let imagePreview = '';

                   @if (isset($submission) && $submission->image)
                       imagePreview = `
                           <div class="row mt-2" id="imgPreview">
                               <div class="col-3">
                                   <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}" target="_blank">
                                       <img src="{{ asset('storage/participant/submission/image/' . $submission->image) }}" class="img-fluid" alt="image">
                                   </a>
                               </div>
                           </div>
                       `;
                   @endif

                   const imageError =
                       '@error('image')<p class="text-danger">{{ $message }}</p>@enderror';
                   const attachmentHtml = `
                       <div class="mb-6 col-md-6">
                           <label class="form-label" for="image">${setting.attachment_name} <code>(optional)</code></label>
                           <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" />
                           ${oldImage ? `<small class="text-muted">Previous file: ${oldImage}</small>` : ''}
                           ${imagePreview}
                           ${imageError}
                       </div>
                   `;
                   $('#attachmentContainer').html(attachmentHtml);
               }

               // Handle Conflict of Interest
               if (setting.is_conflict_of_interest_required) {
                   const oldConflict = `{{ old('conflict_of_interest', @$submission->conflict_of_interest) }}`;
                   const oldConflictOption =
                       `{{ old('has_conflict_of_interest', @$submission->conflict_of_interest ? 'yes' : '') }}`;
                   const conflictError =
                       '@error('conflict_of_interest')<p class="text-danger">{{ $message }}</p>@enderror';
                   const conflictOptionError =
                       '@error('has_conflict_of_interest')<p class="text-danger">{{ $message }}</p>@enderror';

                   const conflictHtml = `
                       <div class="col-md-12 form-group mb-3">
                           <label class="form-label">Do you have any Conflict of Interest? <code>*</code></label><br>
                           <div class="form-check form-check-inline">
                               <input class="form-check-input" type="radio" name="has_conflict_of_interest" id="conflict_yes" value="yes" ${oldConflictOption === 'yes' ? 'checked' : ''} required>
                               <label class="form-check-label" for="conflict_yes">Yes</label>
                           </div>
                           <div class="form-check form-check-inline">
                               <input class="form-check-input" type="radio" name="has_conflict_of_interest" id="conflict_no" value="no" ${oldConflictOption === 'no' ? 'checked' : ''} required>
                               <label class="form-check-label" for="conflict_no">No</label>
                           </div>
                           ${conflictOptionError}
                       </div>
                       <div class="col-md-12 form-group mb-3" id="conflictDetailsWrapper" style="display: none;">
                           <label for="conflict_of_interest" class="form-label">Conflict of Interest Details <code>*</code></label>
                           <textarea class="form-control @error('conflict_of_interest') is-invalid @enderror" name="conflict_of_interest" id="conflict_of_interest" rows="3">${oldConflict}</textarea>
                           ${conflictError}
                       </div>
                   `;
                   $('#conflictOfInterestContainer').html(conflictHtml);

                   // Toggle conflict details visibility
                   $('input[name="has_conflict_of_interest"]').on('change', function() {
                       if ($(this).val() === 'yes') {
                           $('#conflictDetailsWrapper').show();
                           $('#conflict_of_interest').attr('required', true);
                       } else {
                           $('#conflictDetailsWrapper').hide();
                           $('#conflict_of_interest').attr('required', false);
                           $('#conflict_of_interest').val('');
                       }
                   });

                   // Trigger on page load
                   if (oldConflictOption === 'yes') {
                       $('#conflictDetailsWrapper').show();
                   }
               }

               // Handle Source of Funding
               if (setting.is_source_of_funding_required) {
                   const oldFunding = `{{ old('source_of_funding', @$submission->source_of_funding) }}`;
                   const oldFundingOption =
                       `{{ old('has_source_of_funding', @$submission->source_of_funding ? 'yes' : '') }}`;
                   const fundingError =
                       '@error('source_of_funding')<p class="text-danger">{{ $message }}</p>@enderror';
                   const fundingOptionError =
                       '@error('has_source_of_funding')<p class="text-danger">{{ $message }}</p>@enderror';

                   const fundingHtml = `
                       <div class="col-md-12 form-group mb-3">
                           <label class="form-label">Do you have any Source of Funding? <code>*</code></label><br>
                           <div class="form-check form-check-inline">
                               <input class="form-check-input" type="radio" name="has_source_of_funding" id="funding_yes" value="yes" ${oldFundingOption === 'yes' ? 'checked' : ''} required>
                               <label class="form-check-label" for="funding_yes">Yes</label>
                           </div>
                           <div class="form-check form-check-inline">
                               <input class="form-check-input" type="radio" name="has_source_of_funding" id="funding_no" value="no" ${oldFundingOption === 'no' ? 'checked' : ''} required>
                               <label class="form-check-label" for="funding_no">No</label>
                           </div>
                           ${fundingOptionError}
                       </div>
                       <div class="col-md-12 form-group mb-3" id="fundingDetailsWrapper" style="display: none;">
                           <label for="source_of_funding" class="form-label">Source of Funding Details <code>*</code></label>
                           <textarea class="form-control @error('source_of_funding') is-invalid @enderror" name="source_of_funding" id="source_of_funding" rows="3">${oldFunding}</textarea>
                           ${fundingError}
                       </div>
                   `;
                   $('#sourceOfFundingContainer').html(fundingHtml);

                   // Toggle funding details visibility
                   $('input[name="has_source_of_funding"]').on('change', function() {
                       if ($(this).val() === 'yes') {
                           $('#fundingDetailsWrapper').show();
                           $('#source_of_funding').attr('required', true);
                       } else {
                           $('#fundingDetailsWrapper').hide();
                           $('#source_of_funding').attr('required', false);
                           $('#source_of_funding').val('');
                       }
                   });

                   // Trigger on page load
                   if (oldFundingOption === 'yes') {
                       $('#fundingDetailsWrapper').show();
                   }
               }
           }

           function showDefaultFields() {
               // Destroy existing CKEditor instances
               for (let key in ckeditorInstances) {
                   if (ckeditorInstances[key]) {
                       ckeditorInstances[key].destroy();
                   }
               }
               ckeditorInstances = {};

               // Clear all containers
               $('#contentSectionsContainer').empty();
               $('#attachmentContainer').empty();
               $('#conflictOfInterestContainer').empty();
               $('#sourceOfFundingContainer').empty();

               // Recreate default abstract content
               const abstractError =
                   '@error('abstract_content')<p class="text-danger">{{ $message }}</p>@enderror';
               const abstractHtml = `
                   <div class="col-md-12 form-group mb-3">
                       <label for="abstract_content" class="form-label">Abstract Content <code>*
                           (Word Limit: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'infinity' }})</code></label>
                       <textarea class="form-control @error('abstract_content') is-invalid @enderror" name="abstract_content" id="description2" cols="30" rows="5">{{ !empty(old('abstract_content')) ? old('abstract_content') : @$submission->abstract_content }}</textarea>
                       ${abstractError}
                   </div>
               `;
               $('#contentSectionsContainer').html(abstractHtml);

               // Recreate default attachment field
               @if ($setting->attachment_name)
                   const submissionImage = '{{ @$submission->image }}';
                   let imagePreview = '';
                   const imageError =
                       '@error('image')<p class="text-danger">{{ $message }}</p>@enderror';

                   @if (isset($submission) && $submission->image)
                       imagePreview = `
                           <div class="row mt-2" id="imgPreview">
                               <div class="col-3">
                                   <a href="{{ asset('storage/participant/submission/image/' . $submission->image) }}" target="_blank">
                                       <img src="{{ asset('storage/participant/submission/image/' . $submission->image) }}" class="img-fluid" alt="image">
                                   </a>
                               </div>
                           </div>
                       `;
                   @endif

                   const attachmentHtml = `
                       <div class="mb-6 col-md-6">
                           <label class="form-label" for="image">{{ $setting->attachment_name }}
                               <code>{{ $setting->attachment_required == true ? '*' : '(optional)' }}</code></label>
                           <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" ${submissionImage ? '' : '{{ $setting->attachment_required == true ? 'required' : '' }}'} />
                           ${imagePreview}
                           ${imageError}
                       </div>
                   `;
                   $('#attachmentContainer').html(attachmentHtml);
               @endif

               // Re-initialize default CKEditor
               ckeditorInstances['description2'] = CKEDITOR.replace('description2', {
                   filebrowserUploadUrl: '{{ route('ckeditor.fileUpload', ['_token' => csrf_token()]) }}',
                   filebrowserUploadMethod: "form",
                   extraPlugins: 'wordcount',
                   wordcount: {
                       showWordCount: true,
                       maxWordCount: {{ @$setting->abstract_word_limit ? @$setting->abstract_word_limit : 'Infinity' }},
                   }
               });
           }

           const keywordInput = document.querySelector('#keyWord');
           if (keywordInput) {
               new Tagify(keywordInput, {
                   maxTags: {{ @$setting->key_word_limit ?? 9999 }}
               });
           }
       </script>
   @endsection
