<div class="modal-overlay" id="globalConfirmModal">
    <div class="modal-dialog">
        <form method="POST" id="confirmModalForm">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h3 class="modal-title" id="confirmModalTitle">Confirm Action</h3>
                <button type="button" class="sidebar-toggle-btn" onclick="closeConfirmModal()">&times;</button>
            </div>
            <div class="modal-body" id="confirmModalMessage">
                Are you sure you want to proceed with this operation?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Cancel</button>
                <button type="submit" class="btn btn-danger" id="confirmModalSubmitBtn">Confirm</button>
            </div>
        </form>
    </div>
</div>
