<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <!-- Header -->
    <div class="d-flex align-items-start gap-3 mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width: 46px; height: 46px;">
            <i class="icon-base ti tabler-category text-primary fs-4"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">Bulk Recommend Category Change</h5>
            <p class="text-muted small mb-0">
                Send a category change recommendation to
                <strong class="text-primary">{{ count($submissionIds) }}</strong> presenter(s) at once.
            </p>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
        <div class="d-flex gap-2 align-items-start">
            <i class="icon-base ti tabler-info-circle text-info fs-5 flex-shrink-0 mt-1"></i>
            <div>
                <p class="mb-1 small fw-semibold">How this works:</p>
                <ul class="mb-0 ps-3 small text-muted">
                    <li>Each presenter will receive an email with the recommendation.</li>
                    <li>Submissions already in the selected category will be skipped automatically.</li>
                    <li>Only submissions without any existing pending request are included.</li>
                </ul>
            </div>
        </div>
    </div>

    <form id="bulkConvertArticleCategoryForm"
          action="{{ route('submission.bulkConvertArticleCategoryRequest', [$society, $conference]) }}"
          method="POST">
        @csrf
        <input type="hidden" name="ids" id="bulkCategorySubmissionIds" value="{{ json_encode($submissionIds) }}">

        <!-- Target Category -->
        <div class="mb-4">
            <label for="bulk_requested_article_type_id" class="form-label fw-semibold">
                <i class="icon-base ti tabler-arrows-exchange me-1 text-primary"></i>
                Recommend Change To <span class="text-danger">*</span>
            </label>
            <select name="requested_article_type_id" id="bulk_requested_article_type_id"
                    class="form-select form-select-lg" required>
                <option value="">-- Select New Presentation Category --</option>
                @foreach ($articleTypes as $articleType)
                    <option value="{{ $articleType->id }}">{{ $articleType->name }}</option>
                @endforeach
            </select>
            <div class="form-text mt-1">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                Submissions already in the selected category will be automatically skipped.
            </div>
        </div>

        <!-- Selected Submissions List -->
        <div class="mb-4">
            <label class="form-label fw-semibold">
                <i class="icon-base ti tabler-list me-1 text-primary"></i>
                Selected Submissions ({{ $submissions->count() }} eligible)
            </label>
            <div class="border rounded-3 overflow-auto" style="max-height: 220px;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="font-size: 12px;">#</th>
                            <th style="font-size: 12px;">Title</th>
                            <th style="font-size: 12px;">Presenter</th>
                            <th style="font-size: 12px;">Current Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $i => $submission)
                            <tr>
                                <td style="font-size: 12px;">{{ $i + 1 }}</td>
                                <td style="font-size: 12px;" class="text-truncate" style="max-width: 180px;">
                                    {{ \Illuminate\Support\Str::words($submission->title, 6, '...') }}
                                </td>
                                <td style="font-size: 12px;">
                                    {{ $submission->presenter?->fullName($submission->presenter) ?? 'N/A' }}
                                </td>
                                <td style="font-size: 12px;">
                                    <span class="badge bg-label-info">
                                        {{ $submission->articleType?->name ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (count($submissionIds) > $submissions->count())
                <div class="form-text text-warning mt-1">
                    <i class="icon-base ti tabler-alert-triangle me-1"></i>
                    {{ count($submissionIds) - $submissions->count() }} submission(s) excluded (already have a pending request or invalid selection).
                </div>
            @endif
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="icon-base ti tabler-x me-1"></i>Cancel
            </button>
            <button type="submit" class="btn btn-primary" id="sendBulkCategoryRequestBtn"
                    {{ $submissions->isEmpty() ? 'disabled' : '' }}>
                <i class="icon-base ti tabler-send me-1"></i>
                Send to {{ $submissions->count() }} Presenter(s)
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    var form = document.getElementById('bulkConvertArticleCategoryForm');
    var btn = document.getElementById('sendBulkCategoryRequestBtn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
        });
    }
})();
</script>
