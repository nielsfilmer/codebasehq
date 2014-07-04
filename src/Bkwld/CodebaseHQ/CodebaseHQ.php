<?php namespace Bkwld\CodebaseHQ;

use Bkwld\CodebaseHQ\Api\Request;

class CodebaseHQ {

    /**
     * @var array
     */
    protected $config;


    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * @param $user
     */
    public function setUser($user)
    {
        $this->config['api']['username'] = $user;
    }


    /**
     * @param $key
     */
    public function setKey($key)
    {
        $this->config['api']['key'] = $key;
    }


    /**
     * @param $project
     */
    public function setProject($project)
    {
        $this->config['project'] = $project;
    }


    /**
     * @return Request
     */
    protected function makeRequest()
    {
        $request = new Request(
            $this->config['api']['username'],
            $this->config['api']['key'],
            $this->config['project']
        );

        $use_https = isset($this->config['api']['use_https']) ? $this->config['api']['use_https'] : FALSE;
        $request->setHttps($use_https);

        return $request;
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
        $request = $this->makeRequest();
        $answer = $request->call($repo.'/commits/'.$ref);
        $commits = $answer->extractCommits();
        $return = [];

        $found = FALSE;
        $c = 0;

        while(preg_match('#^20\d$#', $answer->getStatus()) && count($commits) > 0) {

            foreach($commits as $commit) {
                if($commit->ref == $start) {
                    $found = TRUE;
                    break;
                }
                $return[] = $commit;
            }

            if($found) break;

            $answer = $request->call($repo.'/commits/'.$ref."?page={$c}");
            $commits = $answer->extractCommits();
            $c++;
        }

        return $return;
    }


    public function tickets($search)
    {

    }
}