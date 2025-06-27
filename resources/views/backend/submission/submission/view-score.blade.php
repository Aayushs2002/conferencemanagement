<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <div class="rounded-top">
        <h5 class="modal-title" id="exampleModalCenterTitle">Assign To Expert <span class="text-danger">(Topic:
                {{ $submission->title }})</span></h5>
        <hr class="py-4">

        <div class="row closeModal"> 

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Introduction/Background</p>
                <span>
                    {{ $submission->submissionRating->introduction }}

                </span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Methods</p>
                <span>{{ $submission->submissionRating->method }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Result/Findings</p>
                <span>{{ $submission->submissionRating->result }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Conclusion</p>
                <span>
                    {{ $submission->submissionRating->conclusion }}

                </span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Grammar/Languages</p>
                <span>
                    {{ $submission->submissionRating->grammar }}

                </span>
            </div>




        </div>
    </div>
</div>
