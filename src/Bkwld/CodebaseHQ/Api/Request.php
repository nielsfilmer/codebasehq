<?php namespace Bkwld\CodebaseHQ\Api;

class Request {

    protected $headers = array('Accept: application/xml', 'Content-type: application/xml');
    protected $uri = 'api3.codebasehq.com';

    protected $user;
    protected $key;
    protected $project;

    protected $use_https = FALSE;


    /**
     * @param string $user
     * @param string $key
     * @param string $project
     */
    public function __construct($user, $key, $project) {
        $this->user = $user;
        $this->key = $key;
        $this->project = $project;
    }


    /**
     * @param bool $use_https
     */
    public function setHttps($use_https)
    {
        $this->use_https = $use_https;
    }


    /**
     * Make a request on the CodebaseHQ API
     * @param $path
     * @param string $method
     * @param Payload $payload
     * @return Answer
     */
    public function call($path, $method = 'GET', Payload $payload = NULL) {

        // Endpoint
        $path = trim($path, '/');
        $url = $this->baseUrl().'/'.$path;

        // Make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->auth());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new Answer($status, new \SimpleXMLElement($result));
    }


    /**
     * Constructs the basic auth string
     * @return string
     */
    protected function auth()
    {
        return $this->user.':'.$this->key;
    }


    /**
     * Makes the base url for api calls
     * @return string
     */
    protected function baseUrl()
    {
        $protocol = ($this->use_https) ? 'https://' : 'http://';
        return $protocol.$this->uri.'/'.$this->project;
    }
}