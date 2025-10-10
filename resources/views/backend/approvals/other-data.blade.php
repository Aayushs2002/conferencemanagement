@extends('backend.layouts.main')
@section('title')
    Other Data
@endsection
@section('content')
    <div class="container mt-5">
        <h3>Pending User-submitted Data</h3>

        <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#institutions">Institutions</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#designations">Designations</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#departments">Departments</a></li>
        </ul>

        <div class="tab-content mt-4">
            <div class="tab-pane fade show active" id="institutions">
                <x-approval-table type="institution" :others="$otherInstitutions" :existing="$institutions" />
            </div>
            <div class="tab-pane fade" id="designations">
                <x-approval-table type="designation" :others="$otherDesignations" :existing="$designations" />
            </div>
            <div class="tab-pane fade" id="departments">
                <x-approval-table type="department" :others="$otherDepartments" :existing="$departments" />
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    const targetSelect = this.closest('tr').querySelector('.merge-target');
                    const target_id = targetSelect.value;

                    fetch('{{ route('admin.approvals.approve') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                type,
                                id,
                                target_id
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                notyf.success(data.message);
                                setTimeout(() => location.reload(), 800);
                            } else {
                                notyf.error('Failed to approve.');
                            }
                        });
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;

                    fetch('{{ route('admin.approvals.reject') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                type,
                                id
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                notyf.success(data.message);
                                setTimeout(() => location.reload(), 800);
                            } else {
                                notyf.error('Failed to delete.');
                            }
                        });
                });
            });
        });
    </script>
@endsection
