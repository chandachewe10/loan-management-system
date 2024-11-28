<!-- Password Confirmation Modal for each asset -->
<div class="modal fade" id="passwordConfirmationModal{{ $cheque->id }}" tabindex="-1" role="dialog"
    aria-labelledby="passwordConfirmationModalLabel{{ $cheque->id }}" aria-hidden="true">
    <form action="{{ route('cheque.destroy', $cheque->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')

        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordConfirmationModalLabel{{ $cheque->id }}">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="password{{ $cheque->id }}">Are you sure you want to delete this item? Please enter
                            password to confirm.</label>
                        <input type="password" class="form-control" id="password{{ $cheque->id }}" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Delete</button>
                </div>
            </div>
        </div>
    </form>
</div>
