/* function verifyForm() {
	if(document.getElementById("convertGenBankToSbol20").checked && document.getElementById("uriPrefix").value == "") {
		document.getElementById("uriPrefixGroup").className += " has-error";
		var helpText = "A URI prefix is required for conversion from GenBank to SBOL 2.0";
		var span = document.createElement("span");
		span.setAttribute("class", "help-block");
		span.innerHTML = helpText;
		document.getElementById("uriPrefixGroup").appendChild(span);
		return false;
	}

	return true;
} */

function clearPaste(id) {
	var fileInput = document.getElementById(id);
	fileInput.value = "";
}

function clearFileInput(id) 
{ 
    var oldInput = document.getElementById(id); 

    var newInput = document.createElement("input"); 

    newInput.type = "file"; 
    newInput.id = oldInput.id; 
    newInput.name = oldInput.name; 
    newInput.className = oldInput.className; 
    newInput.style.cssText = oldInput.style.cssText; 

    oldInput.parentNode.replaceChild(newInput, oldInput); 
}

function setUpload() {
	document.getElementById("usePaste").value = "false";
	clearPaste("pastedPrimaryInputFile");
	clearPaste("pastedDiffInputFile");
}

function setPaste() {
	document.getElementById("usePaste").value = "true";
	clearFileInput("primaryInputFile");
	clearFileInput("diffInputFile");
}

function verifyForm() {

}

function displayValidationResult(data, textStatus, jqXHR) {

}

function submitValidationRequest() {
	if(verifyForm()) {
		$.post('validate', buildRequest(), displayValidationResult)
	} else {
		// TODO: Error handling
	}
}