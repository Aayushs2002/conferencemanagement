@extends('backend.layouts.main')
@section('title')
    Testimonial
@endsection
@section('content')
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div class="row card-header flex-column flex-md-row border-bottom mx-0 px-3">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto mt-0">
                    <h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6">Testimonial</h5>
                </div>
                <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mt-0">
                    <div class="dt-buttons btn-group flex-wrap mb-0">
                        <a href="{{ route('testimonial.create') }}" class="btn btn-primary" tabindex="0">
                            <i class="icon-base ti tabler-plus icon-xs me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New</span>
                        </a>
                    </div> 
                </div>
            </div>
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Organization Name</th>
                        <th>Designation</th>
                        <th>Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($testimonials as $testimonial)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $testimonial->name }}</td>
                            <td> 
                                <a href="{{ asset('storage/testimonial/image/' . $testimonial->image) }}" target="_blank">
                                    <img src="{{ asset('storage/testimonial/image/' . $testimonial->image) }}" alt="image" height="50" width="40">
                                </a>
                            </td>
                            <td>{{ $testimonial->organization_name }}</td>
                            <td>{{ $testimonial->designation }}</td>
                            <td>
                                <div class="star-rating-display">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= ($testimonial->rating ?? 0) ? 'filled' : '' }}">★</span>
                                    @endfor
                                    <small class="text-muted ms-2">({{ $testimonial->rating ?? 0 }}/5)</small>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="icon-base ti tabler-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('testimonial.edit', $testimonial->id) }}">
                                            <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                        </a>
                                        <hr>
                                        <form action="{{ route('testimonial.destroy', $testimonial->id) }}" method="POST">
                                            @method('delete')
                                            @csrf
                                            <a class="dropdown-item text-danger delete" href="javascript:void(0);">
                                                <i class="icon-base ti tabler-trash me-1"></i> Delete
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-pricing">
                <div class="modal-content" id="modalContent"></div>
            </div>
        </div>
    </div>

    <style>
        .star-rating-display {
            display: inline-flex;
            align-items: center;
        }
        
        .star-rating-display .star {
            font-size: 1.2rem;
            color: #ddd;
            margin-right: 0.1rem;
            transition: none;
        }
        
        .star-rating-display .star.filled {
            color: #ffc107;
        }
    </style>
@endsection