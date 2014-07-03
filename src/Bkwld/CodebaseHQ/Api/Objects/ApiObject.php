<?php namespace Bkwld\CodebaseHQ\Api\Objects;


abstract class ApiObject {

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }


    /**
     * Magic getter from attributes from the xml
     * @param $name
     * @return \SimpleXMLElement[]
     */
    public function __get($name)
    {
        return (string) $this->xml->$name;
    }


    /**
     * Returns the Object as a raw XML string
     * @return String
     */
    public function getRaw()
    {
        return $this->xml->asXML();
    }

}