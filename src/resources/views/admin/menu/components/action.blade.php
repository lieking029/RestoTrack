<!-- resources/views/admin/menu/components/action.blade.php -->

<div class="btn-group" role="group">
    <!-- View Button -->
    <a href="{{ route('admin.menu.show', $menu->id) }}" 
       class="btn btn-sm btn-info" 
       title="View Details"
       data-bs-toggle="tooltip">
        <i class="fas fa-eye"></i>
    </a>
    
    <!-- Edit Button -->
    <a href="{{ route('admin.menu.edit', $menu->id) }}" 
       class="btn btn-sm btn-primary" 
       title="Edit Menu Item"
       data-bs-toggle="tooltip">
        <i class="fas fa-edit"></i>
    </a>
    
    <!-- Delete Button -->
    <button type="button" 
            class="btn btn-sm btn-danger" 
            title="Delete Menu Item"
            data-bs-toggle="tooltip"
            onclick="deleteMenu('{{ $menu->id }}', '{{ $menu->name }}')">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deleteMenu(id, name) {
    Swal.fire({
        title: 'Delete Menu Item?',
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
            form.action = `/admin/menus/${id}`;
            
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