<div class="btn-group" role="group">
    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info btn-sm" title="View Details">
        <i class="fas fa-eye"></i>
    </a>
    {{-- @if(auth()->user()->isAdmin() || auth()->user()->isHod() || 
        (auth()->user()->isPoc() && $vendor->internal_poc_id == auth()->id())) --}}
    <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm" title="Edit Vendor">
        <i class="fas fa-edit"></i>
    </a>
    {{-- @endif --}}
    {{-- @if(auth()->user()->isAdmin() || auth()->user()->isFounder()) --}}
    <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-danger btn-sm" 
                title="Delete Vendor"
                onclick="confirmDelete(this)">
            <i class="fas fa-trash"></i>
        </button>
    </form>
    {{-- @endif --}}
</div>

<script>
function confirmDelete(button) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('form').submit();
        }
    });
}
</script> 