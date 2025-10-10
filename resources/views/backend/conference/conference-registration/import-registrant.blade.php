<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div>
        <h4 class="mb-4" style="background: white;">Import Registrant</h4>
        <form id="excelForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12 form-group mb-3">
                    <label for="excel_file" class="pb-1">
                        Import Registrant <code>(Only .xls/.xlsx/.csv)</code>
                    </label>
                    <input type="file" class="form-control" name="excel_file" id="excel_file" />
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-12 text-end"> 
                    <button type="submit" id="importRegistration" class="btn btn-primary">
                        Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#importRegistration').on('click', function(e) {
            e.preventDefault();

            var form = $('#excelForm')[0];
            var data = new FormData(form);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{ route('conference.conference-registration.importExcelSubmit', [$society, $conference]) }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#importRegistration')
                        .attr('disabled', true)
                        .append(
                            '<span class="spinner-border spinner-border-sm ms-2"></span>');
                },
                success: function(response) {
                    $('#importRegistration').attr('disabled', false).text('Import');

                    if (response.type === 'log') {
                        window.location.href = response.file; // trigger download
                        notyf.success(response.message);
                    } else if (response.type === 'success') {
                        $(".modal").modal("hide");
                        notyf.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        notyf.error(response.message || 'Something went wrong.');
                    }
                },

                error: function(xhr) {
                    $('#importRegistration').attr('disabled', false).text('Import');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.excel_file) {
                            $('#excel_file').addClass('is-invalid');
                            $('#excel_file').next('.invalid-feedback').text(errors
                                .excel_file[0]);
                        }
                    } else {
                        notyf.error('An unexpected error occurred. Please try again.');
                    }
                }
            });
        });

        $('#excel_file').on('change', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').text('');
        });
    });
</script>
