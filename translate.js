$(document).ready(function () {
	$('#wpbody-content .wrap').height($(window).height() - 40);
});

function c_t(){
	var value = $('#t_close').attr('value');
	if (value == 'close') {
		$('#wpbody-content').hide('slow');
		$('#t_close').attr('value','show');
	}else if (value == 'show') {
		$('#wpbody-content').show('slow');
		$('#t_close').attr('value','close');
	}
}
