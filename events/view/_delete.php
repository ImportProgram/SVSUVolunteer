<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDelete" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
        <div class="modal-content bg-gradient-danger">

            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-notification">Delete Event</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="py-3 text-center">
                    <i class="ni ni-bell-55 ni-3x"></i>
                    <h4>Are you sure you want to delete <?php echo $event["title"]; ?>?</h4>
                </div>

            </div>

            <div class="modal-footer">
                <form method="post" action="">
                    <button type="submit" class="btn btn-white" name="delete">I am sure!</button>
                </form>
                <button type="button" class="btn btn-link text-white ml-auto" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>