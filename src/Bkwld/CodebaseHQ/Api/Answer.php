<?php namespace Bkwld\CodebaseHQ\Api;

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
    public function commits()
    {
        return $this->extract('commit', '\Bkwld\CodebaseHQ\Api\Objects\Commit');
    }


    /**
     * Extracts the tickets from the answer
     * @return array
     */
    public function tickets()
    {
        return $this->extract('ticket', '\Bkwld\CodebaseHQ\Api\Objects\Ticket');
    }


    /**
     * Extracts the users from the answer
     * @return array
     */
    public function users()
    {
        return $this->extract('user', '\Bkwld\CodebaseHQ\Api\Objects\User');
    }


    /**
     * Generates ApiObjects from the answer
     * @param $attribute
     * @param $api_object
     * @return array
     */
    protected function extract($attribute, $api_object)
    {
        $this->checkStatus();

        $return = [];

        foreach($this->xml->$attribute as $element) {
            $return[] = new $api_object($element);
        }

        return $return;
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