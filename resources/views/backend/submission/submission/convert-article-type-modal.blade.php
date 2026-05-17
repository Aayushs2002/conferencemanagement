<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <!-- Header -->
    <div class="d-flex align-items-start gap-3 mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width: 46px; height: 46px;">
            <i class="icon-base ti tabler-category text-primary fs-4"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">Recommend Category Change</h5>
            <p class="text-muted small mb-0">Send a recommendation to the presenter to change the presentation category.</p>
        </div>
    </div>

    <!-- Submission Info -->
    <div class="alert alert-light border rounded-3 mb-4 py-3 px-3">
        <div class="row g-2">
            <div class="col-12">
                <p class="text-muted small mb-1">Submission Title</p>
                <p class="fw-semibold mb-0">{{ $submission->title }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-muted small mb-1 mt-2">Current Presentation Category</p>
                <span class="badge bg-warning text-dark fs-6 py-2 px-3">
                    <i class="icon-base ti tabler-tag me-1"></i>
                    {{ $submission->articleType?->name ?? 'N/A' }}
                </span>
            </div>
            <div class="col-md-6">
                <p class="text-muted small mb-1 mt-2">Presenter</p>
                <p class="fw-semibold mb-0 small">
                    <i class="icon-base ti tabler-user me-1"></i>
                    {{ $submission->presenter?->fullName($submission->presenter) ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('submission.convertArticleCategoryRequest', [$society, $conference, $submission->id]) }}"
          method="POST" id="convertArticleCategoryForm">
        @csrf

        <div class="mb-4">
            <label for="requested_article_type_id" class="form-label fw-semibold">
                <i class="icon-base ti tabler-arrows-exchange me-1 text-primary"></i>
                Recommend Change To <span class="text-danger">*</span>
            </label>
            <select name="requested_article_type_id" id="requested_article_type_id"
                    class="form-select form-select-lg" required>
                <option value="">-- Select New Presentation Category --</option>
                @foreach ($articleTypes as $articleType)
                    <option value="{{ $articleType->id }}">{{ $articleType->name }}</option>
                @endforeach
            </select>
            <div class="form-text text-muted mt-2">
                <i class="icon-base ti tabler-info-circle me-1"></i>
                The presenter will receive an email with a link to accept or decline this recommendation.
            </div>
        </div>

        @if ($articleTypes->isEmpty())
            <div class="alert alert-warning border-0">
                <i class="icon-base ti tabler-alert-triangle me-2"></i>
                No other presentation categories available to recommend.
            </div>
        @endif

        <div class="d-flex gap-2 justify-content-end mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                <i class="icon-base ti tabler-x me-1"></i>Cancel
            </button>
            <button type="submit" class="btn btn-primary" {{ $articleTypes->isEmpty() ? 'disabled' : '' }}
                    id="sendCategoryRequestBtn">
                <i class="icon-base ti tabler-send me-1"></i>Send Recommendation
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    document.getElementById('convertArticleCategoryForm')?.addEventListener('submit', function (e) {
        var btn = document.getElementById('sendCategoryRequestBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
    });
})();
</script>
