<?php namespace Bkwld\CodebaseHQ\Api;

class Request {

    /**
     * Inject dependencies
     * @param string $user API Username
     * @param string $key API Key
     * @param string $project API Project
     */
    public function __construct($user, $key, $project) {
        $this->user = $user;
        $this->key = $key;
        $this->project = $project;
    }


    /**
     * Make a request on the CodebaseHQ API
     * @param $path
     * @param string $method
     * @param Payload $payload
     * @return Answer
     */
    public function call($path, $method = 'GET', Payload $payload = NULL) {

        // Default headers
        $headers = array('Accept: application/xml', 'Content-type: application/xml');

        // Create basic-auth syntax
        $auth = $this->user.':'.$this->key;

        // Endpoint
        $path = trim($path, '/');
        $url = 'http://api3.codebasehq.com/'.$this->project.'/'.$path;

        // Make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
     * Retrieves a commit or commit tree
     * @param $repo
     * @param $ref
     * @param null $start
     * @return array
     */
    public function commits($repo, $ref, $start = NULL)
    {
        $start = ($start) ?: $ref;
        $answer = $this->call($repo.'/commits/'.$ref);
        $commits = $answer->extractCommits();
        $return = [];

        $found = FALSE;
        $c = 0;

        while(preg_match('#^20\d$#', $answer->getStatus()) && count($commits) > 0) {

            foreach($commits as $commit) {
                $return[] = $commit;
                if($commit->ref == $start) {
                    $found = TRUE;
                    break;
                }
            }

            if($found) break;

            $answer = $this->call($repo.'/commits/'.$ref."?page={$c}");
            $commits = $answer->extractCommits();
            $c++;
        }

        return $return;
    }

}