<div id="pointSheetModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
        <form action="{{route('pointSheet')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="modal-header">
                <h4 class="modal-title">POINT SHEET</h4>
            </div>
            <div class="form-group">
                <div class="modal-body">
                    <label for="pointSheeExcel">Input the sheet number:</label>
                    <input type="number" name="pointSheetInput" class="form-control" id="sheetNumber" min="1" max="1000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary saveSheet" data-dismiss="modal" id="saveSheet">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
        </div>

    </div>
</div>