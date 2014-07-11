<?php namespace Bkwld\CodebaseHQ\Api\Objects;


abstract class ApiObject {

    /**
     * @var \SimpleXMLElement
     */
    protected $xml;


    /**
     * @var array
     */
    protected $attribute_map = [];


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
        if(!isset($this->attribute_map[$name])) {
            return (string) $this->xml->$name;
        } else {
            $value = $this->xml;
            foreach($this->attribute_map[$name] as $attribute) {
                $value = $value->$attribute;
            }

            return (string) $value;
        }
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