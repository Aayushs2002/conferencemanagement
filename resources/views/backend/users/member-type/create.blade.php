@extends('backend.layouts.society.main')

@section('title')
    {{ isset($memberType) ? 'Edit' : 'Add' }} Member Type
@endsection
@section('content')
    <div class="col-md">
        <div class="card">
            <h4 class="card-header"><a href="{{ route('memberType.index', $society) }}"><i
                        class="ti tabler-arrow-narrow-left"></i></a>
                {{ isset($memberType) ? 'Edit' : 'Add' }} Member Type</h4>
            <div class="card-body">
                <form class="needs-validation"
                    action="{{ isset($memberType) ? route('memberType.update', [$society, $memberType->id]) : route('memberType.store', $society) }}"
                    method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @isset($memberType)
                        @method('patch')
                    @endisset
                    <div class="row mb-4">
                        <div class="col-md-2 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="is_society_member" id="is_society_member"
                                value="1"
                                @if (isset($memberType)) {{ $memberType->is_society_member == 1 ? 'checked' : '' }} @else @checked(old('is_society_member') == '1') @endif />
                            <label for="is_society_member" class="form-check-label">Is Society Member ? </label>
                        </div>
                        <div class="col-md-4 form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="requires_student_verification"
                                   id="requires_student_verification" value="1"
                                   @if (isset($memberType)) {{ $memberType->requires_student_verification == 1 ? 'checked' : '' }} @else @checked(old('requires_student_verification') == '1') @endif />
                            <label for="requires_student_verification" class="form-check-label">
                                Requires Student/Resident Verification?
                            </label>
                        </div>
                    </div>
                    <div class="row">

                        <div class="mb-6 col-md-6">
                            <label for="delegate" class="form-label">Delegate <code>*</code></label>
                            <select class="form-select" name="delegate" id="delegate" required>
                                <option hidden>-- Select Delegate --</option>
                                <option value="1"
                                    @if (isset($memberType)) {{ $memberType->delegate == '1' ? 'selected' : '' }} @else @selected(old('delegate') == '1') @endif>
                                    National</option>
                                <option value="2"
                                    @if (isset($memberType)) {{ $memberType->delegate == '2' ? 'selected' : '' }} @else @selected(old('delegate') == '2') @endif>
                                    International</option>
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select delegate.</div>
                            @error('delegate')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6 col-md-6" id="memberTypeWrapper">
                            <label class="form-label" for="type">Member Type <code>*</code></label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" id="type"
                                placeholder="Enter Type" name="type"
                                value="{{ !empty(old('type')) ? old('type') : @$memberType->type }}" required />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please Enter Type.</div>
                            @error('type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($memberType) ? 'Update' : 'Submit' }}</button>
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
        document.addEventListener("DOMContentLoaded", function() {
            const isSocietyMember = document.getElementById("is_society_member");
            const wrapper = document.getElementById("memberTypeWrapper");
            const currentType = "{{ isset($memberType) ? $memberType->type : '' }}"; 

            function fetchMemberTypes() {
                fetch("{{ route('memberType.fetch', $society) }}")
                    .then(response => response.json())
                    .then(data => {
                        let options = `<option hidden>-- Select Member Type --</option>`;
                        data.forEach(item => {
                            let selected = item.type === currentType ? 'selected' : '';
                            options += `<option value="${item.type}" ${selected}>${item.type}</option>`;
                        });

                        wrapper.innerHTML = `
                        <label class="form-label" for="type">Member Type <code>*</code></label>
                        <select class="form-select" name="type" id="type" required>
                            ${options}
                        </select>
                        <div class="invalid-feedback">Please select member type.</div>
                    `;
                    })
                    .catch(err => {
                        console.error("Fetch error:", err);
                        alert("Something went wrong fetching member types!");
                    });
            }

            function showInputField() {
                wrapper.innerHTML = `
                <label class="form-label" for="type">Member Type <code>*</code></label>
                <input type="text" class="form-control" id="type" placeholder="Enter Type" name="type" value="${currentType}" required />
                <div class="invalid-feedback">Please Enter Type.</div>
            `;
            }

            isSocietyMember.addEventListener("change", function() {
                if (this.checked) {
                    fetchMemberTypes();
                } else {
                    showInputField();
                }
            });

            if (isSocietyMember.checked) {
                fetchMemberTypes();
            } else {
                showInputField(); 
            }
        });
    </script>
@endsection
