<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">View Detail </h5>
        <div class="row">
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Name</p>
                <span>{{ $registrant->user->fullName($registrant->user) }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Transaction Id</p>
                <span>{{ $registrant->transaction_id }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Amount</p>
                <span>{{ $registrant->amount }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Meal Type</p>
                <span>
                    @if ($registrant->meal_type == 1)
                        Veg
                    @elseif ($registrant->meal_type == 2)
                        Non-Veg
                    @else
                        N/A
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
