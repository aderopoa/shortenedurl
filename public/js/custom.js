// Offset for Site Navigation
$('#siteNav').affix({
	offset: {
		top: 100
	}
});

$(document).ready(function() {
	$('#short_url_form').submit(function() {
		event.preventDefault();

		var formData = $(this).serialize();


		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: formData
		}).done(function(response) {
			data = JSON.parse(response);
			var message;
			var error_message = $('#error_messages');
			var result_message = $('#success_result');
			error_message.hide();
			result_message.hide();

			// Make sure that the formMessages div has the 'success' class.
			if (data['status'] == 'success') {
				$('#copyTarget').val(data['tiny_url']);
				result_message.show();
			} else {
				message = data['message'];
				error_message.html(message);
				error_message.show();
			}

		});
	});
});

document.getElementById("copyButton").addEventListener("click", function() {
	event.preventDefault();
	copyToClipboard(document.getElementById("copyTarget"));
});

function copyToClipboard(elem) {
	// create hidden text element, if it doesn't already exist
	var targetId = "_hiddenCopyText_";
	var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
	var origSelectionStart, origSelectionEnd;
	if (isInput) {
		// can just use the original source element for the selection and copy
		target = elem;
		origSelectionStart = elem.selectionStart;
		origSelectionEnd = elem.selectionEnd;
	} else {
		// must use a temporary form element for the selection and copy
		target = document.getElementById(targetId);
		if (!target) {
			var target = document.createElement("textarea");
			target.style.position = "absolute";
			target.style.left = "-9999px";
			target.style.top = "0";
			target.id = targetId;
			document.body.appendChild(target);
		}
		target.textContent = elem.textContent;
	}
	// select the content
	var currentFocus = document.activeElement;
	target.focus();
	target.setSelectionRange(0, target.value.length);

	// copy the selection
	var succeed;
	try {
		succeed = document.execCommand("copy");
	} catch(e) {
		succeed = false;
	}
	// restore original focus
	if (currentFocus && typeof currentFocus.focus === "function") {
		currentFocus.focus();
	}

	if (isInput) {
		// restore prior selection
		elem.setSelectionRange(origSelectionStart, origSelectionEnd);
	} else {
		// clear temporary content
		target.textContent = "";
	}
	return succeed;
}