/*
	Alpha by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function($) {

	skel.breakpoints({
		wide: '(max-width: 1680px)',
		normal: '(max-width: 1280px)',
		narrow: '(max-width: 980px)',
		narrower: '(max-width: 840px)',
		mobile: '(max-width: 736px)',
		mobilep: '(max-width: 480px)'
	});

	$(function() {

		var	$window = $(window),
			$body = $('body'),
			$header = $('#header'),
			$banner = $('#banner');

		// Fix: Placeholder polyfill.
			$('form').placeholder();

		// Prioritize "important" elements on narrower.
			skel.on('+narrower -narrower', function() {
				$.prioritize(
					'.important\\28 narrower\\29',
					skel.breakpoint('narrower').active
				);
			});

		// Dropdowns.
			$('#nav > ul').dropotron({
				alignment: 'right'
			});

		// Off-Canvas Navigation.

			// Navigation Button.
				$(
					'<div id="navButton">' +
						'<a href="#navPanel" class="toggle"></a>' +
					'</div>'
				)
					.appendTo($body);

			// Navigation Panel.
				$(
					'<div id="navPanel">' +
						'<nav>' +
							$('#nav').navList() +
						'</nav>' +
					'</div>'
				)
					.appendTo($body)
					.panel({
						delay: 500,
						hideOnClick: true,
						hideOnSwipe: true,
						resetScroll: true,
						resetForms: true,
						side: 'left',
						target: $body,
						visibleClass: 'navPanel-visible'
					});

			// Fix: Remove navPanel transitions on WP<10 (poor/buggy performance).
				if (skel.vars.os == 'wp' && skel.vars.osVersion < 10)
					$('#navButton, #navPanel, #page-wrapper')
						.css('transition', 'none');

		// Header.
		// If the header is using "alt" styling and #banner is present, use scrollwatch
		// to revert it back to normal styling once the user scrolls past the banner.
		// Note: This is disabled on mobile devices.
			if (!skel.vars.mobile
			&&	$header.hasClass('alt')
			&&	$banner.length > 0) {

				$window.on('load', function() {

					$banner.scrollwatch({
						delay:		0,
						range:		0.5,
						anchor:		'top',
						on:			function() { $header.addClass('alt reveal'); },
						off:		function() { $header.removeClass('alt'); }
					});

				});

			}

	});

})(jQuery);

function scrollto(id,ajuste){
	if(!ajuste)
		ajuste = -160;

	$('html, body').animate({
		scrollTop: $(id).offset().top+ajuste
	}, 1000);
}

if (!!$('#subir').offset()) { // make sure "#menu" element exists
        var stickyTop1 = $(document).height()*0.40; // returns number

        $(window).scroll(function(){ // scroll event

          var windowTop = $(window).scrollTop(); // returns number 

          if (stickyTop1 > windowTop){
          	$('#subir').fadeOut();
          }
          else {
          	$('#subir').fadeIn();
          }

      });

    }


$(document).ready(function () {
    $('#error #success').hide();
    //bind send message here
    $('#button-send').click(sendMessage);
});

/* Contact Form */
function checkEmail(email) {
    var check = /^[\w\.\+-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]{2,6}$/;
    if (!check.test(email)) {
        return false;
    }
    return true;
}


function sendMessage() {
    // receive the provided data
    var name = $("input#name").val();
    var email = $("input#email").val();
    // var subject = $("input#subject").val();
    var phone = $("input#phone").val();
    var message = $("textarea#message").val();

    // message = 'message';

    // check if all the fields are filled
    var error = "";
    if (name == '' || name == 'Nombre') {
    	error+= "- Ingresa tu Nombre.<br>"
    }
    if (email == '' || email == 'Email') {
    	error+= "- Ingresa tu Email.<br>"
    }else{	    	
	    if (!checkEmail(email)) {
	        error+= "- Tu E-mail debe ser correcto.<br>"
	    }
    }
    if (phone == '' || phone == 'Teléfono') {
    	error+= "- Ingresa tu Teléfono.<br>"
    }
    if (message == '' || message == 'Mensaje') {
    	error+= "- Ingresa tu Mensaje.<br>"
    }

    if (error!="") {
        $("#error").html(error);
        $("#error").show();
        return false;
    }else{    	
    	$("#error").html("");

	    // make the AJAX request
	    var dataString = $('#contact_form').serialize();
	    $.ajax({
	        type: "POST",
	        url: 'send_form_email.php',
	        data: dataString,
	        dataType: 'json',
	        beforeSend: function (data) {        	
	            $("#success").show();
	        },
	        success: function (data) {        	
	            if (data.success == 0) {
	                var errors = '<ul><li>';
	                if (data.name_msg != '')
	                    errors += data.name_msg + '</li>';
	                if (data.email_msg != '')
	                    errors += '<li>' + data.email_msg + '</li>';
	                if (data.phone_msg != '')
	                    errors += '<li>' + data.phone_msg + '</li>';
	                if (data.message_msg != '')
	                    errors += '<li>' + data.message_msg + '</li>';
	                // if (data.subject_msg != '')
	                //     errors += '<li>' + data.subject_msg + '</li>';

	                $("#error").show();
	            }
	            else if (data.success == 1) {            	
	                $("#success").removeClass('alert-error').addClass('alert-success').show().html('<p class="alert-close">Su mensaje ha sido enviado con éxito!</p>');
	                $("#success").delay(3000).fadeOut();
	                name = $("input#name").val("");
	                email = $("input#email").val("");
	                // subject = $("input#subject").val("");
	                phone = $("input#phone").val("");
	                message = $("textarea#message").val("");
	            }
	        },
	        error: function (error) {        	
	            $("#error").show().html(error.statusText);
	        }
	    });

	    return false;
    }
}