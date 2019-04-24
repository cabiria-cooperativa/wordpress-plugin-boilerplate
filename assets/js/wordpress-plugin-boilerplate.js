jQuery(document).ready(function($) {
    
    jQuery.ajax({
        type: "POST",
        url: init_ajax.url,
        data: { 
          action: 'hello_world_ajax'
        },
        dataType: "json"
    })
    .done(function(response) {
        console.log(response);
    })
    .fail(function(){
        console.log('failed');
    });

});