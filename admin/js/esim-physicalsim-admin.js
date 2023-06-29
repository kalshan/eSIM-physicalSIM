(function( $ ) {
	'use strict';
/*	 $(document).ready(function(){
            var clear_timer;
            $('#esim-physicalsimImport').on('submit', function(event){
                $('#import').attr('disabled',true);
                $('#message').html('');
                event.preventDefault();
                $.ajax({
                    url:ajaxurl+'?action=esim_physicalsim_ajax_upload',
                    method: "POST",
                    data: new FormData(this),
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(data){
                        if(data.success){
                            $('#total_data').text(data.total_line);
                            //start_import();
                           // clear_timer = setInterval(get_import_data, 2000);
                            $('#message').html('<div class="alert alert-success">IMPORTED SUCCESSFULLY</div>');
                            $('#import').attr('disabled',false);
                        }if(data.error){
                            $('#message').html('<div class="alert alert-danger">'+data.error+'</div>');
                            $('#import').attr('disabled',false);
                        }
                    },
                    error:function(data){
                        $('#import').attr('disabled',false);
                    }
                })
            });
        });*/

})( jQuery );
