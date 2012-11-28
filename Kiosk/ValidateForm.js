// JavaScript Document

$(document).ready(function() {
	$('#SubmitButton').click(function() {

		// Disable the submit button
		$('#contact #SubmitButton').attr("disabled", "disabled");

		// Get the data from the form
		var name=$("input[name=name]").val();
		var email=$("input[name=email]").val();
		var telephone=$("input[name=telephone]").val();
		var message=$('div#message :text,textarea').val();
		var referrer2=$('div#Referrer-Hidden :input[name=referrer2]').val();
		var myResult = 0;

		// Validate the data
		if (name=='') {
			$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
			$('#ErrorMessage1').fadeIn(800).delay(2000).fadeOut(800);
			$("input[name=name]").focus();
			$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
			return false;
		}

		if (email=='') {
			$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
			$('#ErrorMessage2').fadeIn(800).delay(2000).fadeOut(800);
			$("input[name=email]").focus();
			$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
			return false;
		}

		if (telephone=='') {
			$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
			$('#ErrorMessage3').fadeIn(800).delay(2000).fadeOut(800);
			$("input[name=telephone]").focus();
			$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
			return false;
		}

		if (message=='') {
			$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
			$('#ErrorMessage4').fadeIn(800).delay(2000).fadeOut(800);
			$('div#message :text,textarea').focus();
			$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
			return false;
		}

		if (message.length > 800) {
			$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
			$('#ErrorMessage5').fadeIn(800).delay(2000).fadeOut(800);
			$('div#message :text,textarea').focus();
			$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
			return false;
		}

		$.ajax({
			type: "POST",
			url: "ValidateEmail.php",
			cache: false,
			data: {email: email},
			dataType: "text",
			error: function(xhr, status, error) {
				alert('(' + status + ', ' + error + ')');
				$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
				myResult = -1;
				return false;
			},
			success: function(result) {
				// alert("is_email() returned: [" + result + "]");
				if (result == 0) {  // Everything good
					myResult = 0;
					return false;
				}

				else if (result < 7) {  // Format OK but no domain
					$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
					$('#ErrorMessage7').fadeIn(800).delay(2000).fadeOut(800);
					$("input[name=email]").val('');  // Clear field
					$("input[name=email]").focus();
					$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
					myResult = -1;
					return false;
				}

				else {  // Bad format
					$('#ErrorFade').fadeIn(800).delay(2000).fadeOut(800);
					$('#ErrorMessage6').fadeIn(800).delay(2000).fadeOut(800);
					$("input[name=email]").val('');  // Clear field
					$("input[name=email]").focus();
					$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
					myResult = -1;
					return false;
				}
			},
			async: false
		});

		if (myResult != 0) {
			return false;
		}

		var dataString = 'name='+ name + '&email=' + email + '&telephone=' + telephone + '&message=' + message + '&referrer2=' + referrer2;
		$.ajax({
			type: "POST",
			url: "DoEmail.php",
			cache: false,
			data: dataString,
			error: function(xhr, status, error) {
				alert('(' + status + ', ' + error + ')');
				$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
				return false;
			},
			success: function(result) {
				// alert("DoEmail() returned: [" + result + "]");

				// Check to see if the mail was successfully sent
				if (result=='Mail sent') {
					$('div#message :text,textarea').val('');  // Clear the message text area
					$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
					self.location.href = 'thankyou.html';  // If everything is OK, go to 'Thank You' page...
					return false;
				}

				else {
					$('div#message :text,textarea').val('');  // Clear the message text area
					$('#contact #SubmitButton').attr("disabled", "");  // Re-activate the submit button
					self.location.href = 'emailFailed.html';  // If mail sending failed, go to 'Try Later' page...
					return false;
				}
			},
			async: false
		});

		return false;
	});
});