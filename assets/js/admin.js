jQuery( document ).ready(function($) {

	jQuery( '.ccbpress-slack-help.button' ).click( function() {
		jQuery('button#contextual-help-link').trigger('click');
		return false;
	});

});
