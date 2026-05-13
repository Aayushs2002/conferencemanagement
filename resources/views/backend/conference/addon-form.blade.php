<div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="">
        <b class="text-center mb-4">Add On for <code>(Conference:
                {{ $conference->conference_theme }})</code></b> 

    </div>
    <div class="modal-body">
        <div class="table-responsive"> 
            <div class="table-responsive">
                <form action="#" method="POST" enctype="multipart/form-data" id="conferenceAddonForm">
                    @csrf
                    <input type="hidden" name="conference_id" value="{{ $conference->id }}">
                    
                    @if(isset($addonsByName) && count($addonsByName) > 0)
                        @foreach($addonsByName as $addonName => $addons)
                            <div class="addon-group mb-4 border p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">{{ $addonName }}</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-addon-group">Remove Addon</button>
                                </div>
                                
                                <input type="hidden" name="addon_names[]" value="{{ $addonName }}">
                                
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Member Type</th>
                                            <th>Early Bird Amount</th>
                                            <th>Regular Amount</th>
                                            <th>Late Amount</th>
                                            <th>On-Site Amount</th>
                                            <th>Guest Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($memberTypes as $memberType)
                                            @php
                                                $addon = $addons->firstWhere('member_type_id', $memberType->id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <label>{{ $loop->iteration }}. {{ $memberType->type }} 
                                                        <small class="text-danger">(Amount in {{ $memberType->delegate == 1 ? 'Rs.' : '$' }})</small>
                                                    </label>
                                                    <input type="hidden" name="member_type_ids[{{ $addonName }}][]" value="{{ $memberType->id }}">
                                                    <input type="hidden" name="addon_ids[{{ $addonName }}][]" value="{{ $addon->id ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                        name="early_bird_amounts[{{ $addonName }}][]"
                                                        value="{{ $addon->early_bird_amount ?? '' }}"
                                                        placeholder="Enter early bird amount" 
                                                        class="form-control" />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                        name="regular_amounts[{{ $addonName }}][]"
                                                        value="{{ $addon->regular_amount ?? '' }}" 
                                                        placeholder="Enter regular amount"
                                                        class="form-control" />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                        name="late_amounts[{{ $addonName }}][]"
                                                        value="{{ $addon->late_amount ?? '' }}" 
                                                        placeholder="Enter late amount"
                                                        class="form-control" />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                        name="on_site_amounts[{{ $addonName }}][]"
                                                        value="{{ $addon->on_site_amount ?? '' }}" 
                                                        placeholder="Enter on-site amount"
                                                        class="form-control" />
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" 
                                                        name="guest_amounts[{{ $addonName }}][]"
                                                        value="{{ $addon->guest_amount ?? '' }}" 
                                                        placeholder="Enter guest amount"
                                                        class="form-control" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif
                    
                    <div id="new-addons-container"></div>
                    
                    <div class="text-center mt-3">
                        <button type="button" id="add-new-addon" class="btn btn-success">Add New Addon</button>
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary" id="submitData">
                            {{ (isset($addonsByName) && count($addonsByName) > 0) ? 'Update' : 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .border-danger {
        border-color: #dc3545 !important;
    }

    .text-danger {
        font-size: 0.85rem;
    }
    
    .addon-group {
        background-color: #f8f9fa;
    }
</style>

<script>
    $(document).ready(function() {
        let addonCounter = 0;
        
        // Add new addon group
        $('#add-new-addon').click(function() {
            let addonName = prompt("Enter Add-on Name:");
            if (!addonName || addonName.trim() === '') {
                return;
            }
            
            addonName = addonName.trim();
            addonCounter++;
            
            let memberTypesHtml = '';
            @foreach($memberTypes as $index => $memberType)
                memberTypesHtml += `
                    <tr>
                        <td>
                            <label>{{ $loop->iteration }}. {{ $memberType->type }} 
                                <small class="text-danger">(Amount in {{ $memberType->delegate == 1 ? 'Rs.' : '$' }})</small>
                            </label>
                            <input type="hidden" name="member_type_ids[${addonName}][]" value="{{ $memberType->id }}">
                            <input type="hidden" name="addon_ids[${addonName}][]" value="">
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="early_bird_amounts[${addonName}][]"
                                placeholder="Enter early bird amount" 
                                class="form-control" />
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="regular_amounts[${addonName}][]"
                                placeholder="Enter regular amount"
                                class="form-control" />
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="late_amounts[${addonName}][]"
                                placeholder="Enter late amount"
                                class="form-control" />
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="on_site_amounts[${addonName}][]"
                                placeholder="Enter on-site amount"
                                class="form-control" />
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="guest_amounts[${addonName}][]"
                                placeholder="Enter guest amount"
                                class="form-control" />
                        </td>
                    </tr>
                `;
            @endforeach
            
            let newAddonHtml = `
                <div class="addon-group mb-4 border p-3 rounded">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">${addonName}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-addon-group">Remove Addon</button>
                    </div>
                    
                    <input type="hidden" name="addon_names[]" value="${addonName}">
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Member Type</th>
                                <th>Early Bird Amount</th>
                                <th>Regular Amount</th>
                                <th>Late Amount</th>
                                <th>On-Site Amount</th>
                                <th>Guest Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${memberTypesHtml}
                        </tbody>
                    </table>
                </div>
            `;
            
            $('#new-addons-container').append(newAddonHtml);
        });
        
        // Remove addon group
        $(document).on('click', '.remove-addon-group', function() {
            if (confirm('Are you sure you want to remove this addon?')) {
                $(this).closest('.addon-group').remove();
            }
        });

        $("#submitData").on('click', function(e) {
            e.preventDefault();
            var data = new FormData($('#conferenceAddonForm')[0]);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{ route('conference.addon.submit') }}',
                data: data,
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitData').attr('disabled', true).append(
                        '<span class="spinner spinner-danger ml-2" style="height: 17px; width: 17px;"></span>'
                    );
                    $('.error-text').remove(); 
                },
                success: function(response) {
                    $('#submitData').attr('disabled', false).text('Update');
                    if (response.type === 'success') {
                        $(".modal").modal("hide");
                        notyf.success(response.message);
                    } else {
                        notyf.error(response.message);
                    }
                },
                error: function(xhr) {
                    $('#submitData').attr('disabled', false).text('Update');

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            notyf.error(messages[0]);
                        });
                    } else {
                        notyf.error('An unexpected error occurred.');
                    }
                }
            });
        });
    });
</script>

