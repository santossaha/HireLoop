@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </h5>
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i> Mark All as Read
                    </button>
                </div>

                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-3 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }} hover-shadow transition-all" 
                             id="notification-{{ $notification->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                        {{ $notification->data['title'] }}
                                    </h6>
                                    <p class="mb-1 text-muted">{{ $notification->data['message'] }}</p>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                @if(!$notification->read_at)
                                    <button class="btn btn-outline-primary btn-sm rounded-pill ms-3" 
                                            onclick="markAsRead('{{ $notification->id }}')">
                                        <i class="fas fa-check me-1"></i> Mark as Read
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="far fa-bell fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No notifications found</p>
                        </div>
                    @endforelse

                    <div class="mt-4 px-3 pb-3">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .notification-item {
        border-left: 3px solid transparent;
    }
    .notification-item:not(.bg-light) {
        border-left-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const notification = document.getElementById(`notification-${id}`);
            notification.classList.add('bg-light');
            notification.style.borderLeftColor = 'transparent';
            const button = notification.querySelector('button');
            if (button) {
                button.remove();
            }
            // Add fade effect
            notification.style.opacity = '0.7';
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection 