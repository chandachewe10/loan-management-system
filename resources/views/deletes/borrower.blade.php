<form action="{{ route('borrower.destroy', $borrower->id) }}" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')
   

    <!-- Password Confirmation Modal for each asset -->
    <div class="modal fade" id="passwordConfirmationModal{{ $borrower->id }}" tabindex="-1" role="dialog" aria-labelledby="passwordConfirmationModalLabel{{ $borrower->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordConfirmationModalLabel{{ $borrower->id }}">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="password{{ $borrower->id }}">Are you sure you want to delete this item? Please enter password to confirm.</label>
                        <input type="password" class="form-control" id="password{{ $borrower->id }}" name="password">
                    </div>
                    <!-- Hidden input to store the asset ID -->
                    <input type="hidden" name="asset_id" value="{{ $borrower->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
</form>
