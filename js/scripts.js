function verifyForm() {
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
}

function setUpload() {
	document.getElementById("usePaste").value = "false";
}

function setPaste() {
	document.getElementById("usePaste").value = "true";
}