jQuery(document).ready(function($) {
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
		
		 // Show the loader
        $('#loader').show();

        var form = $(this);
        var formData = form.serialize();
       

        $.ajax({
            url: ajaxfilter.ajaxurl,
            type: 'GET',
            data: formData + '&action=filter_resources',
            success: function(response) {
                
                $('#posts-container').html(response);
            },
            complete: function() {
                // Hide the loader
                $('#loader').hide();
               
            }
        });
    });
});
