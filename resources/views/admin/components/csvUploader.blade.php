<div class="modal fade" id="modal-csv" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">CSV Uploader</h4>
            </div>
            <form action="{{url('/imports/save')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                        <div class="form-group">
                            <label>CSV</label>
                            <input type="file" class="form-control" name="file" required="required">
                            <p class="help-block">Max 500 records at a time</p>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="submit">
                </div>
            </form>
        </div>
    </div>
</div>

<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-csv"><i class="fa fa-cloud-upload"></i></button>