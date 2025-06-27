@extends('backend.layouts.conference.main')
@section('title')
    Submission
@endsection
@section('content')
    <div class="card mb-6">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6"><a
                            href="{{ route('submission.index', [$society, $conference]) }}"><i
                                class="ti tabler-arrow-narrow-left"></i></a> Authors of {{ $submission->title }}
                    </h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">

                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Designation</th>
                        <th scope="col">Institution</th>
                        <th scope="col">Institution Address</th>
                        <th scope="col">Phone</th>
                        {{-- <th scope="col" style="width: 12%">Action</th> --}}

                    </tr>
                </thead>
                @foreach ($authors as $author)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $author->name }}{!! $author->main_author == 1 ? ' <span class="badge bg-success">Main</span>' : '' !!}</td>
                        <td>{{ $author->email }}</td>
                        <td>{{ $author->institution }}</td>
                        <td>{{ $author->institution_address }}</td>
                        <td>{{ $author->designation }}</td>
                        <td>{{ !empty($author->phone) ? $author->phone : '-' }}</td>
                        {{-- <td>
                               <div class="d-flex gap-2">

                                   <a href="#" data-bs-toggle="modal"
                                       data-bs-target="#pricingModal{{ $submission->id }}"
                                       data-topic-id="{{ $submission->id }}" data-author-id="{{ $author->id }}"
                                       class="btn btn-success editAuthor btn-sm" title="Edit Data"><i
                                           class="icon-base ti tabler-pencil me-1"></i> </a>
                                   @if ($loop->iteration != 1)
                                       <form
                                           action="{{ route('my-society.conference.submission.author.destroy', [$conference, $author]) }}"
                                           method="POST">
                                           @method('delete')
                                           @csrf
                                           <button title="Delete Data" class="btn btn-danger btn-sm delete"
                                               type="submit"><i class="icon-base ti tabler-trash me-1"></i></button>
                                       </form>
                                   @endif
                               </div>
                           </td> --}}
                    </tr>
                @endforeach
            </table>
        </div>
        {{-- <div class="modal fade" id="pricingModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-simple modal-pricing">
                   <div class="modal-content" id="modalContent">
                   </div>
               </div>
           </div> --}}
    </div>
@endsection
{{-- @section('scripts')
       <script>
           $(document).ready(function() {
               $(document).on("click", ".viewData", function(e) {
                   e.preventDefault();
                   var url = '{{ route('society.show') }}';
                   var _token = '{{ csrf_token() }}';
                   var id = $(this).data('id');
                   $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                   var data = {
                       _token: _token,
                       id: id
                   };
                   $.post(url, data, function(response) {
                       setTimeout(function() {
                           $('#modalContent').html(response);
                       }, 1000);
                   });
               });

               $(document).on("click", ".addAuthor", function(e) {
                   e.preventDefault();
                   var url = '{{ route('my-society.conference.submission.author.create', $conference) }}';
                   var _token = '{{ csrf_token() }}';
                   var topicId = $(this).data('topic-id');
                   $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                   var data = {
                       _token: _token,
                       topicId: topicId
                   };
                   $.post(url, data, function(response) {
                       setTimeout(function() {
                           $('#modalContent').html(response);
                       }, 1000);
                   });
               });

               $(document).on("click", ".editAuthor", function(e) {
                   var url = '{{ route('my-society.conference.submission.author.create', $conference) }}';
                   var _token = '{{ csrf_token() }}';
                   var topicId = $(this).data('topic-id');

                   var authorId = $(this).data('author-id');
                   $('#modalContent').html(`
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                   var data = {
                       _token: _token,
                       topicId: topicId,
                       authorId: authorId
                   };
                   $.post(url, data, function(response) {
                       $('.modal-content').html(response);
                   });
               });
           });
       </script>
   @endsection --}}
