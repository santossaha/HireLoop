<div class="btn-group" role="group">
    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info btn-sm" title="View Details">
        <i class="fas fa-eye"></i>
    </a>
    @if(auth()->user()->isAdmin() || auth()->user()->isHod() || 
        (auth()->user()->isPoc() && $vendor->internal_poc_id == auth()->id()))
    <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm" title="Edit Vendor">
        <i class="fas fa-edit"></i>
    </a>
    @endif
    @if(auth()->user()->isAdmin() || auth()->user()->isFounder())
    <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" 
                title="Delete Vendor"
                onclick="return confirm('Are you sure you want to delete this vendor?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
    @endif
</div> 