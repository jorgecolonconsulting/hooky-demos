<?php
namespace _2UpMedia\HookyDemo\Guzzle;

use GuzzleHttp\Client;

class CustomClient extends Client
{
    use \_2UpMedia\Hooky\HooksTrait;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        self::$checkCallableParameters = true;
    }

    public function get($url = null, $options = [])
    {
        // you choose if you want hooks to be able to override your arguments by sending them as references
        if (($hookReturn = $this->callBeforeHooks($this, __METHOD__, [$url, &$options])) !== null) {
            return $hookReturn;
        }

        $return = $this->send($this->createRequest('GET', $url, $options));

        // you choose if you want hooks to be able to override the $return value, by sending that value to them
        if (($hookReturn = $this->callAfterHooks($this, __METHOD__, [$url, $options, $return])) !== null) {
            return $hookReturn;
        }

        return $return;
    }
}
 