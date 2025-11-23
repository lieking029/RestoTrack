<!-- resources/views/admin/user/components/action.blade.php -->

<div class="btn-group" role="group">
    <!-- View Button -->
    <a href="{{ route('admin.user.show', $user->id) }}" 
       class="btn btn-sm btn-info" 
       title="View Details"
       data-bs-toggle="tooltip">
        <i class="fas fa-eye"></i>
    </a>
    
    <!-- Edit Button -->
    <a href="{{ route('admin.user.edit', $user->id) }}" 
       class="btn btn-sm btn-primary" 
       title="Edit User"
       data-bs-toggle="tooltip">
        <i class="fas fa-edit"></i>
    </a>
    
    <!-- Delete Button -->
    <button type="button" 
            class="btn btn-sm btn-danger" 
            title="Delete User"
            data-bs-toggle="tooltip"
            onclick="deleteUser('{{ $user->id }}', '{{ $user->full_name }}')">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deleteUser(id, name) {
    Swal.fire({
        title: 'Delete User?',
        html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
        cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/user/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>