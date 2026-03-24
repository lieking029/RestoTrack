<div class="btn-group" role="group">
    @if($product->trashed())
        <button type="button"
                class="btn btn-sm btn-success"
                title="Restore Product"
                data-bs-toggle="tooltip"
                onclick="restoreProduct('{{ $product->id }}', '{{ $product->name }}')">
            <i class="fas fa-undo"></i>
        </button>
    @else
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

        @if(auth()->user()->isAdmin())
        <button type="button"
                class="btn btn-sm btn-warning"
                title="Archive Product"
                data-bs-toggle="tooltip"
                onclick="archiveProduct('{{ $product->id }}', '{{ $product->name }}')">
            <i class="fas fa-archive"></i>
        </button>
        @endif
    @endif
</div>

<script>
function archiveProduct(id, name) {
    Swal.fire({
        title: 'Archive Product?',
        html: `Are you sure you want to archive <strong>${name}</strong>?<br>You can restore it later.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-archive"></i> Yes, Archive',
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

function restoreProduct(id, name) {
    Swal.fire({
        title: 'Restore Product?',
        html: `Are you sure you want to restore <strong>${name}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-undo"></i> Yes, Restore',
        cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/product/${id}/restore`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            form.appendChild(csrfToken);
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