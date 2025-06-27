<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-simple modal-edit-user">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center mb-6">
                    <h4 class="mb-2">Update Your Information</h4>
                    <p>Updating user details will receive a privacy audit.</p>
                </div>
                <form class="needs-validation" enctype="multipart/form-data">
                    <div class="row g-6">
                        <div class="col-12 col-md-6">
                            <label for="institution_id" class="form-label">Institution Name <code>*</code></label>
                            <select class="form-select" name="institution_id" id="institution_id" required>
                                <option value="" hidden>-- Select Institution Name --</option>
                                @foreach ($institutions as $institution)
                                    <option value="{{ $institution->id }}" @selected(old('institution_id') == $institution->id)>
                                        {{ $institution->name }}</option>
                                @endforeach
                                <option value="other" @selected(old('institution_id') == 'other')>Others</option>
                            </select>
                            @error('institution_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6" id="otherInstitutionWrapper" style="display: none;">
                            <label for="other_institution_name" class="form-label">Other Institution Name</label>
                            <input type="text" class="form-control" name="other_institution_name"
                                id="other_institution_name" placeholder="Enter Institution Name"
                                value="{{ old('other_institution_name') }}">
                            @error('other_institution_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="designation_id" class="form-label">Designation <code>*</code></label>
                            <select class="form-select" name="designation_id" id="designation_id" required>
                                <option value="" hidden>-- Select Designation --</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" @selected(old('designation_id') == $designation->id)>
                                        {{ $designation->designation }}</option>
                                @endforeach
                            </select>
                            @error('designation_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="department_id" class="form-label">Department <code>*</code></label>
                            <select class="form-select" name="department_id" id="department_id" required>
                                <option value="" hidden>-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="institute_address">Institution Address<code>*</code></label>
                            <input type="text" id="institute_address" name="institute_address" class="form-control"
                                required placeholder="Enter Institution Address"
                                value="{{ old('institute_address') }}">
                            @error('institute_address')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        @if (
                            (current_user()->userDetail->name_prefix_id == 1 || current_user()->userDetail->name_prefix_id == 3) &&
                                current_user()->userDetail->country_id == 125)
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="council_number">Medical Council
                                    Number<code>*</code></label>
                                <input type="text" id="council_number" name="council_number" class="form-control"
                                    required placeholder="Enter Medical Council Number"
                                    value="{{ old('council_number') }}">
                                @error('council_number')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="col-12 col-md-6">
                            <label class="form-label" for="image">Photo <code>*(Passport Sized Image)</code></label>
                            <input type="file" id="image" name="image" required class="form-control" />
                            <div class="row" id="imgPreview"></div>
                            @error('image')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary me-3">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
        profileModal.show();

        const institutionSelect = document.getElementById('institution_id');
        const otherInstitutionWrapper = document.getElementById('otherInstitutionWrapper');

        function toggleOtherInstitution() {
            otherInstitutionWrapper.style.display = institutionSelect.value === 'other' ? 'block' : 'none';
        }

        institutionSelect.addEventListener('change', toggleOtherInstitution);
        toggleOtherInstitution();

        const form = document.querySelector('form.needs-validation');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            document.querySelectorAll('.text-danger').forEach(el => el.remove());
            form.querySelectorAll('input, select').forEach(input => input.classList.remove(
                'is-invalid'));

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            const formData = new FormData(form);
            formData.append('_method', 'PATCH');
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('profile.update-profile') }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) return response.json().then(data => Promise.reject(data));
                    return response.json();
                })
                .then(data => {
                    if (data.type === 'success') {
                        notyf.success(data.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        notyf.error(data.message || 'Something went wrong.');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';

                    if (error.errors) {
                        for (const [key, messages] of Object.entries(error.errors)) {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorEl = document.createElement('div');
                                errorEl.className = 'text-danger';
                                errorEl.textContent = messages[0];
                                input.insertAdjacentElement('afterend', errorEl);
                            }
                        }
                    } else {
                        notyf.error('An unexpected error occurred.');
                    }
                });
        });
    });
</script>
