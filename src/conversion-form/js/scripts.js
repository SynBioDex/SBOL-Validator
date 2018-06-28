var mainFileInput = new FileReader();

function mainFileChanged() {
	mainFileInput.readAsText(document.getElementById('primaryInputFile').files[0], 'utf-8');
}

function clearPaste(id) {
	var fileInput = document.getElementById(id);
	fileInput.value = "";
}

function clearFileInput(id) {
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
	document.getElementById("usePaste").value = false;
	clearPaste("pastedPrimaryInputFile");
}

function setPaste() {
	document.getElementById("usePaste").value = true;
	clearFileInput("primaryInputFile");
}

function handleFailOnFirst() {
	var failOnFirst = document.getElementById("failOnFirstError");

	if(failOnFirst.checked) {
		allowStackTrace();
	} else {
		disallowStackTrace();
	}
}

function allowStackTrace() {
	var printStackTrace = document.getElementById("displayFullStackTrace");

	printStackTrace.disabled = false;
}

function disallowStackTrace() {
	var printStackTrace = document.getElementById("displayFullStackTrace");

	printStackTrace.disabled = true;
	printStackTrace.checked = false;
}

function verifyForm() {
	if(!mainFileGiven()) {
		alert("Main file not given");
		return false;
	}

	return true;
}

function mainFileGiven() {
	var pastedGiven = document.getElementById("pastedPrimaryInputFile").value != "";
	var fileGiven = document.getElementById("primaryInputFile").value != "";

	if(paste()) {
		return pastedGiven;
	} else {
		return fileGiven;
	}
}

function paste() {
	return document.getElementById("usePaste").value == 'true';
}

function getPrimaryFileString() {
	if(paste()) {
		return document.getElementById("pastedPrimaryInputFile").value;
	} else {
		return mainFileInput.result;
	}
}

function buildRequest() {
	var fullRequest = { };
	var mainFile = getPrimaryFileString();

	options = buildOptions();

	fullRequest = {
		"options": options,
        "main_file": mainFile,
        "return_file": true,
    };

	return fullRequest;
}

function returnFalseForEmpty(value) {
	if(value === "") {
		return false;
	} else {
		return value;
	}
}

function getSubsetUri() {
	var value = document.getElementById("topLevelToConvert").value;
	return returnFalseForEmpty(value);
}

function getVersion() {
	var value = document.getElementById("version").value;
	return returnFalseForEmpty(value);
}

function getUriPrefix() {
	var value = document.getElementById("uriPrefix").value;
	return returnFalseForEmpty(value);
}

function buildOptions() {
	var options = { };

	options["language"] = getOutputLanguage();
	options["subset_uri"] = getSubsetUri();
	options["fail_on_first_error"] = false;
	options["provide_detailed_stack_trace"] = false;
	options["check_uri_compliance"] = false;
	options["check_completeness"] = false;
	options["check_best_practices"] = false;
	options["uri_prefix"] = getUriPrefix();
	options["version"] = getVersion();
	options["test_equality"] = false;

	if(!paste()) {
		options["main_file_name"] = getMainFileName();
	}

	return options;
}

function getMainFileName() {
	var mainFileName = document.getElementById("primaryInputFile").files[0].name;

	return mainFileName;
}

function getOutputLanguage() {
	var SBOL1 = document.getElementById("sbol11");
	var SBOL2 = document.getElementById("sbol20");
	var SBML = document.getElementById("sbml");
	var GenBank = document.getElementById("genbank");
	var FASTA = document.getElementById("fasta");

	if(SBOL1.checked) {
		return "SBOL1";
	} else if(SBOL2.checked) {
		return "SBOL2";
	} else if(GenBank.checked) {
		return "GenBank";
	} else if(FASTA.checked) {
		return "FASTA";
	} else if(SBML.checked) {
		return "SBML";
	}
}

function parseData(data) {
	var toReturn = [];
	console.log(data);
	if(data["valid"]) {
			if(data["errors"][data["errors"].length - 1] === "Conversion failed.") {
				toReturn = toReturn.concat(data["errors"]);
			} else {
				toReturn.push("<a href='" + data["output_file"] + "'>Validated and converted file</a>");
			}
	} else {
		toReturn = toReturn.concat(data["errors"]);
	}

	return toReturn;
}

function displayValidationResult(data, textStatus, jqXHR) {
	var interpreted = parseData(data);
	document.getElementById("result").innerHTML = interpreted.join("<br>");
	document.getElementById("myModal").style.display = "block";
}

function apiError(data, textStatus, jqXHR) {
	console.log(data);
	alert("There was an error submitting your request. Try refreshing and submitting again. If the problem persists, try clearing your cache.");
}

function submitValidationRequest() {
	if(verifyForm()) {
		$.ajax({
			url: '/validate/',
			data: JSON.stringify(buildRequest()),
			success: displayValidationResult,
			error: apiError,
			type: 'POST',
			contentType: 'application/json'})
	}
}

function closeModal() {
	document.getElementById("myModal").style.display = "none";
}

window.onclick = function(event) {
	modal = document.getElementById("myModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
