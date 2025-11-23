<div class="modal-header">
    <h5 class="modal-title" id="pricingModalLabel">{{ isset($articleType) ? 'Edit' : 'Add' }} Article Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="needs-validation" id="articleTypeForm" novalidate>
        @csrf

        @isset($articleType)
            @method('patch')
            <input type="hidden" name="id" value="{{ $articleType->id }}">
        @endisset

        <div class="row">
            <div class="mb-6 col-md-12">
                <label class="form-label" for="name">Article Type Name<code>*</code></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                    id="name" placeholder="Enter Article Type Name" name="name"
                    value="{{ old('name') ?? @$articleType?->name }}" required />
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter Article Type Name.</div>
                @error('name')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    {{ isset($articleType) ? 'Update' : 'Submit' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {

        // Prevent duplicate event bindings
        $(document).off("submit", "#articleTypeForm");

        $(document).on("submit", "#articleTypeForm", function (e) {
            e.preventDefault();

            // HTML5 validation
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            let formData = new FormData(this);

            // Ensure output is a valid JS string
            let url = {!! json_encode(isset($articleType)
                ? route('articleType.update', [$society, $conference, $articleType->id])
                : route('articleType.store', [$society, $conference])
            ) !!};

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function (response) {
                    $('#pricingModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then(() => location.reload());
                },

                error: function (xhr) {
                    let errors = xhr.responseJSON?.errors;

                    if (errors) {
                        let errorMessage = Object.values(errors)
                            .flat()
                            .join('\n');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorMessage
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred'
                        });
                    }
                }
            });
        });
    });
</script>

