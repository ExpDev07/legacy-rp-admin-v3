<!-- A modal component -->
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="{{ $target }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header">
                <!-- Title -->
                <h5 class="modal-title">
                    {{ $title }}
                </h5>
                <!-- Closing of modal -->
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                {{ $slot }}
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button class="btn btn-dark" type="button" data-dismiss="modal">Close</button>
                {{ $actions }}
            </div>
        </div>
    </div>
</div>