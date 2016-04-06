<?php
class APIRequest extends ValidationRequest {
    var $metadata;
    var $mainFile;
    var $diffFile;
    var $wantFileBack;

    function __construct($json) {
        parent::__construct();
        $this->metadata = $json["validationOptions"];
        $this->mainFile = $this->decodeJSONEnclosedFile($json["mainFile"]);
        $this->wantFileBack = $json["wantFileBack"];
        if($this->metadata["diff"]) {
            $this->diffFile = $this->decodeJSONEnclosedFile($json["diffFile"]);
        }

        $this->process();
    }

    function decodeJSONEnclosedFile($file) {
        return base64_decode($file);
    }

    function process() {
        $this->fileOptions->convertSbol11To20 = $this->metadata["sbol11To20"];
        $this->fileOptions->convertSbol20ToGenBank = $this->metadata["sbol20ToGenBank"];
        $this->fileOptions->convertGenBankToSbol20 = $this->metadata["genBankToSbol20"];
        $this->fileOptions->performFileDiff = $this->metadata["diff"];
        $this->validationOptions->allowNonCompliantUri = $this->metadata["noncompliantUrisAllowed"];
        $this->validationOptions->allowIncompleteDocument = $this->metadata["incompleteDocumentsAllowed"];
        $this->validationOptions->checkBestPractices = $this->metadata["bestPracticesCheck"];
        $this->validationOptions->failOnFirstError = $this->metadata["failOnFirstError"];
        $this->validationOptions->displayFullStackTrace = $this->metadata["displayFullErrorStackTrace"];

        $this->fileOptions->componentDefinitionUri = $this->metadata["ComponentDefinitionUri"];
        $this->conversionOptions->uriPrefix = $this->metadata["uriPrefix"];
        $this->conversionOptions->version = $this->metadata["version"];

        $this->sbolFile = $this->doUpload($this->mainFile);
        if($this->fileOptions->performFileDiff) {
            $this->comparisonFile = $this->doUpload($this->diffFile);
        }

        if(!$this->wantFileBack) {
            $this->outputFile = uniqid();
        }
    }

    function doUpload($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . ".upload";
        $uploadOk = 1;
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