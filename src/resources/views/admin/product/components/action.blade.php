<div class="btn-group" role="group">
    <a href="{{ route('admin.product.show', $product->id) }}" 
       class="btn btn-sm btn-info" 
       title="View Details"
       data-bs-toggle="tooltip">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ route('admin.product.edit', $product->id) }}" 
       class="btn btn-sm btn-primary" 
       title="Edit Product"
       data-bs-toggle="tooltip">
        <i class="fas fa-edit"></i>
    </a>
    
    <button type="button" 
            class="btn btn-sm btn-danger" 
            title="Delete Product"
            data-bs-toggle="tooltip"
            onclick="deleteProduct('{{ $product->id }}', '{{ $product->name }}')">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deleteProduct(id, name) {
    Swal.fire({
        title: 'Delete Product?',
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
            form.action = `/admin/product/${id}`;
            
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

document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>