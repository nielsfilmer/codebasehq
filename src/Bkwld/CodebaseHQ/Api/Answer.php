<?php namespace Bkwld\CodebaseHQ\Api;

/**
 * Class Answer
 * Answer from the CodebaseHQ Api
 * @package Bkwld\CodebaseHQ\Api
 */
class Answer
{
    /**#@+
     * Constant object names
     *
     * @var string
     */
    const OBJ_COMMIT = 'commit';
    const OBJ_TICKET = 'ticket';
    const OBJ_USER = 'user';
    const OBJ_PROJECT = 'project';
    /**#@-*/

    /**
     * @var array
     */
    protected static $class_map = array(
        self::OBJ_COMMIT => '\Bkwld\CodebaseHQ\Api\Objects\Commit',
        self::OBJ_TICKET => '\Bkwld\CodebaseHQ\Api\Objects\Ticket',
        self::OBJ_USER => '\Bkwld\CodebaseHQ\Api\Objects\User',
        self::OBJ_PROJECT => '\Bkwld\CodebaseHQ\Api\Objects\Project',
    );

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * @var Integer
     */
    protected $status;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param $status
     * @param \SimpleXMLElement $xml
     * @param Request $request
     */
    public function __construct($status, \SimpleXMLElement $xml, Request $request)
    {
        $this->status = $status;
        $this->xml = $xml;
        $this->request = $request;
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
        return (string)$this->xml->error;
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
        return $this->extract(self::OBJ_COMMIT);
    }


    /**
     * Extracts the tickets from the answer
     * @return array
     */
    public function tickets()
    {
        return $this->extract(self::OBJ_TICKET);
    }


    /**
     * Extracts the users from the answer
     * @return array
     */
    public function users()
    {
        return $this->extract(self::OBJ_USER);
    }


    /**
     * @return array
     */
    public function project()
    {
        return $this->extract(self::OBJ_PROJECT);
    }


    /**
     * Generates ApiObjects from the answer
     * @param $attribute
     * @return array
     */
    protected function extract($attribute)
    {
        $this->checkStatus();

        $api_object = static::$class_map[$attribute];

        // Object is in the root of the answer
        if($this->xml->getName() == $attribute) {
            return new $api_object($this->xml, $this->request);
        }

        // Multiple objects in the answer
        $return = [];

        foreach($this->xml->$attribute as $element) {
            $return[] = new $api_object($element, $this->request);
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