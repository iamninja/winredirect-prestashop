$(document).ready(function() {

	var installmentsSlider = $('#installments-sl').slider({
		value:0,
		tooltip:'always',
		tooltip_position:'top'
	});

	$('#installments-idn').text(installmentsSlider.slider('getValue'));

});
