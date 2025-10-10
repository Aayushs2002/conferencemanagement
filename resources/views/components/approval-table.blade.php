@props(['type', 'others', 'existing'])

<div class="card mb-4">
    <div class="card-header">
        <h5>Pending {{ ucfirst($type) }}s</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Submitted Name</th>
                    <th>Merge With Existing</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($others as $item)
                    {{-- @dd($item) --}}
                    <tr>
                        <td>{{ $item->user->fullName($item->user) }}</td>
                        <td>
                            @if ($type === 'institution')
                                {{ $item->institution_name }}
                            @elseif($type === 'designation')
                                {{ $item->designation_name }}
                            @elseif($type === 'department')
                                {{ $item->department_name }}
                            @endif
                        </td>
                        <td>
                            <select class="form-select merge-target" data-id="{{ $item->id }}"
                                data-type="{{ $type }}">
                                <option value="">-- Select Existing --</option>
                                @foreach ($existing as $option)
                                    @if ($type === 'institution')
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @elseif($type === 'designation')
                                        <option value="{{ $option->id }}">{{ $option->designation }}</option>
                                    @elseif($type === 'department')
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-success btn-approve" data-id="{{ $item->id }}"
                                data-type="{{ $type }}">Approve</button>
                            {{-- <button class="btn btn-danger btn-delete" data-id="{{ $item->id }}"
                                data-type="{{ $type }}">Reject</button> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No pending {{ $type }}s.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
