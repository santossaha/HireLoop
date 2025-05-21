
<!-- Modal -->
<div class="modal fade" id="vendorInvite" tabindex="-1" role="dialog" aria-labelledby="vendorInviteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorInviteLabel">Vendor Invite</h5>
            </div>
            <form method="POST" action="{{route('vendor-email-invite')}}">
                @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="Enter Email" class="form-control" name="email" required autofocus>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Send Link</button>
            </div>
            </form>
        </div>
    </div>
</div>