<div class="modal-body ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <h5 class=" mb-4 " style="background: white;">View Detail <span class="text-danger">(User
                Name:
                {{ $user->fullName($user) }})</span></h5>
        <div class="row">

            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>First Name</p>
                <span>{{ $user->f_name }}</span>
            </div>
            @if ($user->m_name)
                <div class="col-md-4 mb-4">
                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Middle Name</p>
                    <span>{{ $user->m_name }}</span>
                </div>
            @endif
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Last Name</p>
                <span>{{ $user->l_name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email</p>
                <span>{{ $user->email }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Phone</p>
                <span>{{ $user->userDetail->phone }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Gender</p>
                <span>
                    @if ($user->userDetail->gender == 1)
                        Male
                    @elseif ($user->userDetail->gender == 2)
                        Female
                    @elseif ($user->userDetail->gender == 3)
                        Other
                    @endif
                </span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Institution Name</p>
                <span>{{ $user->userDetail->institution->name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Institution Address</p>
                <span>{{ $user->userDetail->address }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Department Name</p>
                <span>{{ $user->userDetail->department->name }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Designation</p>
                <span>{{ $user->userDetail->designation->designation }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Council Number</p>
                <span>{{ $user->userDetail->council_number }}</span>
            </div>
            <div class="col-md-4 mb-4">
                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Country</p>
                <span>{{ $user->userDetail->country->country_name }}</span>
            </div>

        </div>
    </div>
</div>
