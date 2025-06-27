<!-- Modal Body -->
<div class="modal-body">
    <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="text-center mb-4">
        <h4 class="fw-bold">Add New Role</h4>
        <p class="text-muted">Set role permissions</p>
    </div>

    <form id="addRoleForm" class="row g-4">
        <!-- Role Name -->
        <div class="col-12">
            <label for="modalRoleName" class="form-label">Role Name</label>
            <input type="text" class="form-control" id="modalRoleName" name="modalRoleName"
                value="{{ @$role->name }}" placeholder="Enter role name" required>
        </div>

        <!-- Permissions -->
        <div class="col-12">
            <label class="form-label">Permissions</label>

            <!-- Global Select All -->
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label fw-bold text-primary" for="selectAll">Select All Permissions</label>
            </div>

            <!-- Permission Groups Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @php $groupedPermissions = $permissions->groupBy('parent'); @endphp
                @foreach ($groupedPermissions as $parent => $items)
                    <div class="col">
                        <div class="border rounded p-3 h-100 permission-group">
                            <!-- Parent Switch -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input parent-checkbox" type="checkbox"
                                    id="parent-{{ Str::slug($parent) }}" data-group="{{ Str::slug($parent) }}">
                                <label class="form-check-label fw-bold"
                                    for="parent-{{ Str::slug($parent) }}">{{ $parent }}</label>
                            </div>
                            <!-- Permissions in Group -->
                            @foreach ($items as $item)
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox {{ Str::slug($parent) }}"
                                        type="checkbox" value="{{ $item->id }}" name="permission[]"
                                        id="permission-{{ $item->id }}"
                                        @if (isset($role) && $role->hasPermissionTo($item->name)) checked @endif>
                                    <label class="form-check-label" for="permission-{{ $item->id }}">
                                        {{ $item->name }}
                                    </label>
                                </div>
                            @endforeach

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Buttons -->
        <div class="col-12 text-center pt-3">
            <button type="submit" class="btn btn-primary me-2">Submit</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {

        class RolePermissionManager {
            constructor() {
                this.selectAllCheckbox = null;
                this.parentCheckboxes = null;
                this.permissionCheckboxes = null;
                this.form = null;

                this.init();
            }

            init() {
                // Wait for DOM to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => this.initializeElements());
                } else {
                    this.initializeElements();
                }
            }

            initializeElements() {
                this.selectAllCheckbox = document.getElementById('selectAll');
                this.parentCheckboxes = document.querySelectorAll('.parent-checkbox');
                this.permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
                this.form = document.getElementById('addRoleForm');

                if (this.selectAllCheckbox && this.parentCheckboxes.length > 0) {
                    this.bindEvents();
                }
            }

            bindEvents() {
                // Global select all handler
                if (this.selectAllCheckbox) {
                    this.selectAllCheckbox.addEventListener('change', (e) => {
                        this.handleSelectAll(e.target.checked);
                    });
                }

                // Parent checkbox handlers
                this.parentCheckboxes.forEach(parentCheckbox => {
                    parentCheckbox.addEventListener('change', (e) => {
                        this.handleParentChange(e.target);
                    });
                });

                // Individual permission handlers
                this.permissionCheckboxes.forEach(permissionCheckbox => {
                    permissionCheckbox.addEventListener('change', (e) => {
                        this.handlePermissionChange(e.target);
                    });
                });

                // Form submission handler
                if (this.form) {
                    this.form.addEventListener('submit', (e) => {
                        this.handleSubmit(e);
                    });
                }
            }

            handleSelectAll(isChecked) {
                // Update all parent checkboxes
                this.parentCheckboxes.forEach(parentCheckbox => {
                    parentCheckbox.checked = isChecked;
                    parentCheckbox.indeterminate = false;
                });

                // Update all permission checkboxes
                this.permissionCheckboxes.forEach(permissionCheckbox => {
                    permissionCheckbox.checked = isChecked;
                });
            }

            handleParentChange(parentCheckbox) {
                const group = parentCheckbox.dataset.group;

                // Use CSS.escape to handle special characters in group names
                const selector = `.permission-checkbox.${CSS.escape(group)}`;
                const groupPermissions = document.querySelectorAll(selector);

                // Update all permissions in this group
                groupPermissions.forEach(permission => {
                    permission.checked = parentCheckbox.checked;
                });

                // Clear indeterminate state when manually toggled
                parentCheckbox.indeterminate = false;

                // Update select all checkbox
                this.updateSelectAllState();
            }

            handlePermissionChange(permissionCheckbox) {
                // Find the group this permission belongs to
                const groupClasses = Array.from(permissionCheckbox.classList);
                const groupClass = groupClasses.find(cls =>
                    cls !== 'permission-checkbox' &&
                    cls !== 'form-check-input'
                );

                if (groupClass) {
                    // Find the parent checkbox for this group
                    const parentCheckbox = document.querySelector(
                        `.parent-checkbox[data-group="${CSS.escape(groupClass)}"]`
                    );

                    if (parentCheckbox) {
                        // Get all permissions in this group
                        const groupPermissions = document.querySelectorAll(
                            `.permission-checkbox.${CSS.escape(groupClass)}`
                        );

                        const checkedCount = Array.from(groupPermissions).filter(cb => cb.checked).length;
                        const totalCount = groupPermissions.length;

                        // Update parent checkbox state
                        if (checkedCount === 0) {
                            parentCheckbox.checked = false;
                            parentCheckbox.indeterminate = false;
                        } else if (checkedCount === totalCount) {
                            parentCheckbox.checked = true;
                            parentCheckbox.indeterminate = false;
                        } else {
                            parentCheckbox.checked = false;
                            parentCheckbox.indeterminate = true;
                        }
                    }
                }

                // Update select all checkbox
                this.updateSelectAllState();
            }

            updateSelectAllState() {
                if (!this.selectAllCheckbox) return;

                const checkedCount = Array.from(this.permissionCheckboxes).filter(cb => cb.checked).length;
                const totalCount = this.permissionCheckboxes.length;

                if (checkedCount === 0) {
                    this.selectAllCheckbox.checked = false;
                    this.selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    this.selectAllCheckbox.checked = true;
                    this.selectAllCheckbox.indeterminate = false;
                } else {
                    this.selectAllCheckbox.checked = false;
                    this.selectAllCheckbox.indeterminate = true;
                }
            }

            handleSubmit(e) {
                e.preventDefault();

                const formData = new FormData(this.form);
                const roleName = formData.get('modalRoleName');
                const permissions = formData.getAll('permission[]');

                // Validation
                if (!roleName || roleName.trim() === '') {
                    this.showAlert('Please enter a role name', 'warning');
                    document.getElementById('modalRoleName').focus();
                    return false;
                }

                if (permissions.length === 0) {
                    this.showAlert('Please select at least one permission', 'warning');
                    return false;
                }

                // Here you can add your AJAX call to submit data to Laravel backend
                this.submitToServer(roleName, permissions);

                return false;
            }

            submitToServer(roleName, permissions) {
                // Example AJAX submission - replace with your actual endpoint
                fetch('{{ isset($role) ? route('roles.update', [$society, $conference, $role->id]) : route('roles.store', [$society, $conference]) }}', {
                        method: '{{ isset($role) ? 'PUT' : 'POST' }}',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute(
                                    'content') || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: roleName,
                            permissions: permissions
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.showAlert('Role created successfully!', 'success');
                            this.resetForm();
                            // Close modal if using Bootstrap modal
                            const modal = bootstrap?.Modal?.getInstance(document.querySelector(
                                '.modal'));
                            if (modal) {
                                setTimeout(() => modal.hide(), 1500);
                            }
                            window.location.reload();

                        } else {
                            this.showAlert(data.message || 'Error creating role', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showAlert('An error occurred while creating the role', 'danger');
                    });
            }

            showAlert(message, type = 'info') {
                // Create or update alert element
                let alertContainer = document.getElementById('role-alert-container');
                if (!alertContainer) {
                    alertContainer = document.createElement('div');
                    alertContainer.id = 'role-alert-container';
                    alertContainer.className = 'position-fixed top-0 end-0 p-3';
                    alertContainer.style.zIndex = '9999';
                    document.body.appendChild(alertContainer);
                }

                const alertId = 'alert-' + Date.now();
                const alertHtml = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

                alertContainer.insertAdjacentHTML('beforeend', alertHtml);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    const alertElement = document.getElementById(alertId);
                    if (alertElement) {
                        alertElement.remove();
                    }
                }, 5000);
            }

            resetForm() {
                if (this.form) {
                    this.form.reset();
                }

                if (this.selectAllCheckbox) {
                    this.selectAllCheckbox.checked = false;
                    this.selectAllCheckbox.indeterminate = false;
                }

                this.parentCheckboxes.forEach(cb => {
                    cb.checked = false;
                    cb.indeterminate = false;
                });

                this.permissionCheckboxes.forEach(cb => {
                    cb.checked = false;
                });
            }

            // Public method to get selected permissions
            getSelectedPermissions() {
                return Array.from(this.permissionCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
            }

            // Public method to set permissions (useful for editing)
            setSelectedPermissions(permissionIds) {
                this.permissionCheckboxes.forEach(cb => {
                    cb.checked = permissionIds.includes(cb.value);
                });

                // Update parent checkboxes and select all
                this.parentCheckboxes.forEach(parentCb => {
                    this.handlePermissionChange(
                        document.querySelector(
                            `.permission-checkbox.${CSS.escape(parentCb.dataset.group)}`)
                    );
                });
            }
        }
        window.rolePermissionManager = new RolePermissionManager();
    });
</script>

<style>
    .permission-group {
        transition: all 0.2s ease;
        background: #fafafa;
    }

    .permission-group:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background: #ffffff;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-input:indeterminate {
        background-color: #6c757d;
        border-color: #6c757d;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
    }

    .parent-checkbox:indeterminate+.form-check-label {
        opacity: 0.8;
    }

    #role-alert-container {
        max-width: 350px;
    }
</style>
