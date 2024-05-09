<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{asset('assets/plugins/popper/popper.min.js') }}"></script>
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{asset('js/waves.js') }}"></script>
<!--Menu sidebar -->
<script src="{{asset('js/sidebarmenu.js') }}"></script>
<!--stickey kit -->
<script src="{{asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
<script src="{{asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{asset('js/custom.js') }}"></script>
<!--dropzone/drag and drop file--->
<script src="{{asset('js/dropzone.min.js')}}"></script>
<script src="{{asset('js/icheck.js')}}"></script>
<script src="{{asset('js/jquery.bootstrap-touchspin.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-switch/bootstrap-switch.js')}}"></script>



<!-- ============================================================== -->
<!-- This page plugins -->
<!-- ============================================================== -->
<!-- chartist chart -->
<!--c3 JavaScript -->
<script src="{{asset('assets/plugins/d3/d3.min.js') }}"></script>
<script src="{{asset('assets/plugins/c3-master/c3.min.js') }}"></script>
<!-- Chart JS -->
<script src="{{asset('js/dashboard1.js') }}"></script>
<script src="{{asset('js/dashboard1.js') }}"></script>

<!-- ============================================================== -->
<!-- Style switcher -->
<!-- ============================================================== -->
<script src="{{asset('assets/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}"></script>
@yield('scripts')
@stack('scripts')
<script src="{{asset('assets/plugins/html5-editor/wysihtml5-0.3.0.js')}}"></script>
<script src="{{asset('assets/plugins/html5-editor/bootstrap-wysihtml5.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function() {
        $('.textarea_editor').wysihtml5();
        
    });
</script>
<script src="{{asset('js/validation.js')}}"></script>
<script>
    ! function(window, document, $) {
        "use strict";
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(),
            $(".skin-square input").iCheck({
                checkboxClass: "icheckbox_square-green",
                radioClass: "iradio_square-green"
            }),
            $(".touchspin").TouchSpin(), $(".switchBootstrap").bootstrapSwitch();
    }(window, document, jQuery);
</script>
{{-- ajax scripting --}}
<script>
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#saveSheet').click(function(e) {
        console.log("wtf")
        var $number = $('#sheetNumber');
        var numberData = {
            number: $number.val(),
        };
        var formURL = $(this).attr("action");
        var urlPost = "{{route('pointSheet')}}";
        /* start ajax submission process */
        $.ajax({
            url: urlPost,
            type: "POST",
            data: numberData,
            success: function(data, textStatus, jqXHR) {
                alert('Success!');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error occurred!');
            }
        });

        e.preventDefault(); //STOP default action
        
        /* ends ajax submission process */

        $('#datepicker').datepicker({
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'yy',
            onClose: function(dateText, inst) { 
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, 1));
            }
        });
        $("#datepicker").focus(function () {
            $(".ui-datepicker-month").hide();
            $('.ui-datepicker-calendar').css('display','none')
        });
    })
    

});
</script>
<script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" charset="utf8" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('#datatable').DataTable({
            "processing":true,
            "serverSide":true,
            "ajax": "{{route('api.getUserLog')}}",
            "columns":[
                {"data":"description"},
                {"data":"subject_id"},
                {"data":"causer_id"},
                {"data":"created_at"}
            ]
        })
    })
</script>