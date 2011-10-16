$(document).ready(function() {
	
	// show appropriate contact info when contact icon is clicked
	$('#footer-contact ul .contact-icon').click(function(event){
		$('#footer-contact .click-contact-info').hide();
		$('#click-'+$(this).attr('id')).show().parent().show()
	});
	
	// clicking on the page will hide the contact box
	$('html').click(function(){
		$('#footer-contact-click-box').hide();
		$('footer-contact .contact-click-info').hide();
	});

	// clicking in the click box or one one of the icons won't
	// hide the contact box
	$('#footer-contact-click-box').click(function(event){
		event.stopPropagation();
	});
	
	$('#footer-contact .contact-icon').click(function(event){
		event.stopPropagation();
	});
	
});