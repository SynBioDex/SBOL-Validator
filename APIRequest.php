<?php
class APIRequest extends ValidationRequest {
    var $metadata;
    var $mainFile;
    var $diffFile;
    var $wantFileBack;

    function __construct($json) {
        parent::__construct();
        $this->metadata = $json["validationOptions"];
        $this->mainFile = $json["mainFile"];
        $this->wantFileBack = $json["wantFileBack"];
        if($this->metadata["diff"]) {
            $this->diffFile = $json["diffFile"];
        }

        $this->process();
    }


    function process() {
        $this->fileOptions->output = $this->metadata["output"];
        $this->fileOptions->performFileDiff = $this->metadata["diff"];
        $this->validationOptions->allowNonCompliantUri = $this->metadata["noncompliantUrisAllowed"];
        $this->validationOptions->allowIncompleteDocument = $this->metadata["incompleteDocumentsAllowed"];
        $this->validationOptions->checkBestPractices = $this->metadata["bestPracticesCheck"];
        $this->validationOptions->failOnFirstError = $this->metadata["failOnFirstError"];
        $this->validationOptions->displayFullStackTrace = $this->metadata["displayFullErrorStackTrace"];

        $this->fileOptions->topLevelToConvert = $this->metadata["topLevelToConvert"];
        $this->conversionOptions->uriPrefix = $this->metadata["uriPrefix"];
        $this->conversionOptions->version = $this->metadata["version"];

        $this->sbolFile = $this->doUpload($this->mainFile);
        if($this->fileOptions->performFileDiff) {
            $this->comparisonFile = $this->doUpload($this->diffFile);
        }

        if(!$this->wantFileBack) {
            $this->outputFile = "uploads/" . uniqid();
        }
    }

    function doUpload($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . ".upload";
        $addition = 1;

        while (file_exists($target_file)) {
            $pathparts = pathinfo($target_file);
            $target_file = $pathparts["dirname"] . "/" . $pathparts["filename"] . "-" . $addition . "." . $pathparts["extension"];
            $addition = $addition + 1;
        }

        file_put_contents($target_file, $file);

        return $target_file;
    }
}