<?php namespace Bkwld\CodebaseHQ\Api;

use Bkwld\CodebaseHQ\Api\Objects\Commit;

/**
 * Class Answer
 * Answer from the CodebaseHQ Api
 * @package Bkwld\CodebaseHQ\Api
 */
class Answer {

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * @var Integer
     */
    protected $status;

    /**
     * @param $status
     * @param \SimpleXMLElement $xml
     */
    public function __construct($status, \SimpleXMLElement $xml)
    {
        $this->status = $status;
        $this->xml = $xml;
    }


    /**
     * Gets the http status of the answer
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Gets the error as a string
     * @return string
     */
    public function getError()
    {
        return (string) $this->xml->error;
    }


    /**
     * Returns the answer as raw xml string
     * @return String
     */
    public function getRaw()
    {
        return $this->xml->asXML();
    }


    /**
     * Extracts the commits from the answer
     * @return array
     */
    public function extractCommits()
    {
        $this->checkStatus();

        $commits = [];

        foreach($this->xml->commit as $element) {
            $commits[] = new Commit($element);
        }

        return $commits;
    }


    /**
     * Checks if we can handle the answer
     * @throws ApiException
     */
    protected function checkStatus()
    {
        $status = $this->getStatus();

        if (!preg_match('#^20\d$#', $status)) {
            $error = $this->getError();
            throw new ApiException("CodebaseHQ request failure ({$status}): {$error}");
        }
    }
}