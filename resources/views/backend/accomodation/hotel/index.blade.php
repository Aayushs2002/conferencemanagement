@extends('backend.layouts.conference.main')

@section('title')
    Hotel
@endsection
@section('content')
    <div class="card">

        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Hotel</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="icon-base ti tabler-upload icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportTo('excel')">Export to Excel</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('pdf')">Export to PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportTo('csv')">Export to CSV</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="window.print()">Print</a></li>
                            </ul>
                        </div>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Add Hotel'))
                            <a href="{{ route('hotel.create', [$society, $conference]) }}" class="btn btn-primary"
                                tabindex="0">
                                <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Hotel Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Person</th>
                        <th scope="col">Contact Number</th>
                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Featured Hotel'))
                            <th scope="col">Is Featured ?</th>
                        @endif
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hotels as $hotel)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $hotel->name }}</td>
                            <td>{{ $hotel->address }}</td>
                            <td>{{ $hotel->contact_person }}</td>
                            <td>{{ $hotel->phone }}</td>
                            @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Change Featured Hotel'))
                                <td>
                                    @if ($hotel->visible_status == 1)
                                        <a href="{{ route('hotel.changeStatus', $hotel->id) }}"
                                            class="btn btn-sm btn-success ml-2" title="Unfeature Hotel"><i
                                                class="icon-base ti tabler-circle-check"></i></a>
                                    @else
                                        <a href="{{ route('hotel.changeStatus', $hotel->id) }}"
                                            class="btn btn-sm btn-warning ml-2" title="Feature Hotel"><i
                                                class="icon-base ti tabler-circle-x"></i></a>
                                    @endif
                                </td>
                            @endif
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Edit Hotel'))
                                            <a class="dropdown-item"
                                                href="{{ route('hotel.edit', [$society, $conference, $hotel->id]) }}"><i
                                                    class="icon-base ti tabler-pencil me-1"></i> Edit</a>
                                        @endif
                                        <a class="dropdown-item viewData" data-bs-toggle="modal"
                                            data-bs-target="#pricingModal{{ $hotel->id }}"><i
                                                class="icon-base ti tabler-eye me-1 "></i> View</a>

                                        <hr>
                                        @if (auth()->user()->hasConferencePermissionBlade(getConference(request()->segment(4)), 'Delete Hotel'))
                                            <form
                                                action="{{ route('hotel.destroy', [$society, $conference, $hotel->id]) }}"
                                                method="POST">
                                                @method('delete')
                                                @csrf
                                                <a class="dropdown-item text-danger delete" href="javascript:void(0);"><i
                                                        class="icon-base ti tabler-trash me-1"></i> Delete</a>
                                            </form>
                                        @endif
                                    </div>

                                </div>

                            </td>

                        </tr>
                        <div class="modal fade" id="pricingModal{{ $hotel->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                                <div class="modal-content" id="modal-content">

                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <h4 class="modal-title" id="exampleModalCenterTitle">View Hotel Detail</h4>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Hotel Name
                                                </p><span>{{ $hotel->name }}</span>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Address</p>
                                                <span>{{ $hotel->address }}</span>
                                            </div>
                                            @if (!empty($hotel->contact_person))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Contact
                                                        Person</p><span>{{ $hotel->contact_person }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->phone))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Phone
                                                    </p><span>{{ $hotel->phone }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->email))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Email
                                                    </p><span>{{ $hotel->email }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->rating))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Rating
                                                    </p><span>{{ $hotel->rating }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->price))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Price
                                                    </p><span>{{ $hotel->price }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->price))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i
                                                            class="i-ID-2 text-16 mr-1"></i>Website</p>
                                                    <span>{{ $hotel->website }}</span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->featured_image))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Logo
                                                    </p><span><img
                                                            src="{{ asset('storage/hotel/featured-image/' . $hotel->featured_image) }}"
                                                            height="100" width="100" alt="image"></span>
                                                </div>
                                            @endif
                                            @if (!empty($hotel->cover_image))
                                                <div class="col-md-4 mb-4">
                                                    <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Cover
                                                        Image
                                                    </p><span><img
                                                            src="{{ asset('storage/hotel/cover-image/' . $hotel->cover_image) }}"
                                                            height="100" width="100" alt="image"></span>
                                                </div>
                                            @endif
                                        </div>
                                        @if (!empty($hotel->images))
                                            <p class="text-primary mb-1"><i class="i-ID-2 text-16 mr-1"></i>Images</p>
                                            @foreach ($hotel->images as $img)
                                                <div class="col-2 gap-2 mt-1">
                                                    <img src="{{ asset('storage/hotel/images/' . $img['fileName']) }}"
                                                        height="100" alt="img">
                                                    <p class="text-center">{{ $img['room_type'] }}</p>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if (!empty($hotel->description))
                                            <div class="my-2">
                                                <p class="text-primary mb-1"><i
                                                        class="i-Letter-Open text-16 mr-1 mt-1"></i>Description</p>
                                                <p>{!! $hotel->description !!}</p>
                                            </div>
                                        @endif
                                        @if (!empty($hotel->facility))
                                            <div class="my-2">
                                                <p class="text-primary mb-1"><i
                                                        class="i-Letter-Open text-16 mr-1 mt-1"></i>Facility</p>
                                                <p>{!! $hotel->facility !!}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>

            </table>
        </div>
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent">
                </div>
            </div>
        </div>
    </div>
@endsection
