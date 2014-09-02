<?php namespace Bkwld\CodebaseHQ\Api\Objects;

use Bkwld\CodebaseHQ\Api\Request;

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
     * @var \Bkwld\CodebaseHQ\Api\Request
     */
    protected $request;


    /**
     * @param \SimpleXMLElement $xml
     * @param Request $request
     */
    public function __construct(\SimpleXMLElement $xml, Request $request = NULL)
    {
        $this->xml = $xml;
        $this->request = $request;
    }


    /**
     * Magic getter from attributes from the xml
     * @param $name
     * @return \SimpleXMLElement[]
     */
    public function __get($name)
    {
        if(!isset($this->attribute_map[$name])) {
            $value = $this->xml->$name;
        } else {
            $value = $this->xml;
            foreach($this->attribute_map[$name] as $attribute) {
                $value = $value->$attribute;
            }
        }

        if(count($value->children()) > 0) {
            return (array) $value;
        } else return (string) $value;
    }


    /**
     * Magic setter to create and edit the xml
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(!isset($this->attribute_map[$name])) {
            if(!isset($this->xml->$name)) {
                $this->xml->addChild($name, $value);
            } else {
                $this->xml->$name = $value;
            }
        } else {
            $element = $this->xml;
            foreach($this->attribute_map[$name] as $attribute) {
                if(!isset($element->$attribute)) {
                    if(end($this->attribute_map[$name]) == $attribute) {
                        $element->addChild($attribute, $value);
                    } else {
                        $element->addChild($attribute);
                        $element = $element->$attribute;
                    }
                }
            }
        }
    }


    /**
     * Magic to string conversion
     * @return String
     */
    public function __toString()
    {
        return $this->getRaw();
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