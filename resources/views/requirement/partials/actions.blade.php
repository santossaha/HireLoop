<div class="btn-group" role="group">
    <a href="{{ route('requirements.show', $requirement->id) }}" class="btn btn-info btn-sm">
        <i class="fas fa-eye"></i>
    </a>
    
    <a href="{{ route('requirements.edit', $requirement->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i>
    </a>
    
    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $requirement->id }}">
        <i class="fas fa-trash"></i>
    </button>
   
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal{{ $requirement->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $requirement->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $requirement->id }}">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this requirement ({{ $requirement->requirement_id }})?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('requirements.destroy', $requirement->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div> 