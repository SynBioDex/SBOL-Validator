var mainFileInput = new FileReader();
var diffFileInput = new FileReader();

function mainFileChanged() {
	console.log("Main file change recorded");
	mainFileInput.readAsText(document.getElementById('primaryInputFile').files[0]);
}

function diffFileChanged() {
	console.log("Diff file change rcorded");
	diffFileInput.readAsText(document.getElementById('diffInputFile').files[0]);
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
	clearPaste("pastedDiffInputFile");
}

function setPaste() {
	document.getElementById("usePaste").value = true;
	clearFileInput("primaryInputFile");
	clearFileInput("diffInputFile");
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
}

function verifyForm() {
	if(!mainFileGiven()) {
		alert("Main file not given");
		return false;
	}

	if(runningDiff() &&  !diffFileGiven()) {
		alert("Comparison file not given");
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

function diffFileGiven() {
	var pastedGiven = document.getElementById("pastedDiffInputFile").value != "";
	var fileGiven = document.getElementById("diffInputFile").value != "";

	if(paste()) {
		return pastedGiven;
	} else {
		return fileGiven;
	}
}

function runningDiff() {
	var runDiff = document.getElementById("performFileDiff").checked;

	return runDiff;
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

function getDiffFileString() {
	if(paste()) {
		return document.getElementById("pastedDiffInputFile").value;
	} else {
		return diffFileInput.result;
	}
}


function buildRequest() {
	var fullRequest = { };
	var mainFile = getPrimaryFileString();

	options = buildOptions();

	if(runningDiff()) {
		var diffFile = getDiffFileString();
		fullRequest = {
			"options": options,
			"main_file": mainFile,
			"diff_file": diff_file
		};
	} else {
		fullRequest = {
			"options": options,
			"main_file": mainFile,
		};
	}

	console.log(fullRequest);

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
	options["continue_after_first_error"] = !document.getElementById("failOnFirstError").checked;
	options["provide_detailed_stack_trace"] = document.getElementById("displayFullStackTrace").checked;
	options["check_uri_compliance"] = !document.getElementById("allowNonCompliantUris").checked;
	options["check_completeness"] = !document.getElementById("allowIncompleteDocuments").checked;
	options["check_best_practices"] = document.getElementById("checkBestPractices").checked;
	options["uri_prefix"] = getUriPrefix();
	options["version"] = getVersion();
	options["test_equality"] = document.getElementById("performFileDiff").checked;

	return options;
}

function getOutputLanguage() {
	var SBOL1 = document.getElementById("sbol11");
	var SBOL2 = document.getElementById("sbol20");
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
	}
}

function displayValidationResult(data, textStatus, jqXHR) {
	console.log(data);
	console.log(textStatus);
	console.log(jqXHR);
}

function submitValidationRequest() {
	if(verifyForm()) {
		console.log(JSON.stringify(buildRequest()));

		$.ajax({
			url: 'http://localhost:5000/validate',
			data: JSON.stringify(buildRequest()), 
			success: displayValidationResult, 
			type: 'POST',
			contentType: 'application/json'})
	}
}