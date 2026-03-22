@extends('backend.layouts.main')
@section('title')
    My Society
@endsection
@section('content')
    <div class="row mb-12 g-6">
        <h2 class="text-center">My Society</h2>
        @foreach ($joinedSocities as $society)
            <div class="col-md-6 col-lg-6">
                <div class="card h-100">
                    <div class="d-flex justify-content-center">
                        <img class="card-img-top" src="{{ asset('storage/society/logo/' . $society->logo) }}"
                            style="width: 20%" alt="Card image cap" />
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $society->users->where('type', 2)->value('f_name') }}
                            ({{ $society->abbreviation }})
                        </h5>
                        <p class="card-text">
                            {!! $society->description !!}
                        </p>

                        @if($society->pivot->memberType && $society->pivot->memberType->requires_student_verification == 1)
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="mb-3">
                                    <i class="ti-tablershield-check me-1"></i> Student/Resident Verification Documents
                                </h6>

                                <form action="{{ route('mySociety.updateDocuments', $society->id) }}"
                                      method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <i class="ti-tablerid-badge me-1"></i> ID Card
                                            </label>

                                            @if($society->pivot->id_card_document)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/society/student-verification/' . $society->pivot->id_card_document) }}"
                                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti-tablereye me-1"></i> View Uploaded ID Card
                                                    </a>
                                                </div>
                                            @endif

                                            <input type="file" class="form-control" name="id_card_document"
                                                   accept=".jpg,.jpeg,.png,.pdf">
                                            <small class="text-muted">JPG, PNG, or PDF - Max 5MB</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <i class="ti-tablerfile-certificate me-1"></i> Official Letter
                                            </label>

                                            @if($society->pivot->official_letter_document)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/society/student-verification/' . $society->pivot->official_letter_document) }}"
                                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti-tablereye me-1"></i> View Uploaded Letter
                                                    </a>
                                                </div>
                                            @endif

                                            <input type="file" class="form-control" name="official_letter_document"
                                                   accept=".jpg,.jpeg,.png,.pdf">
                                            <small class="text-muted">JPG, PNG, or PDF - Max 5MB</small>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="ti-tablerupload me-1"></i> Upload Documents
                                        </button>

                                        @if($society->pivot->documents_uploaded_at)
                                            <small class="text-muted ms-2">
                                                Last updated: {{ \Carbon\Carbon::parse($society->pivot->documents_uploaded_at)->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        @endif

                        <a href="{{ route('my-society.conference', $society) }}" class="btn btn-outline-primary mt-3">Go To
                            Society</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
