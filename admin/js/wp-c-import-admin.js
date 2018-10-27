(function( $ ) {
	'use strict';

    $(function() {
    	let $manual_import_btn = $('#opt-manualimport-buttonset1');

    	$manual_import_btn.click(function(){
    		if ( confirm('Are you sure?') ) {
                $.post(ajaxurl, { 'action': 'product_import' }, function() {
                    alert('All done!');
                });
			}
		});
	});
})( jQuery );
