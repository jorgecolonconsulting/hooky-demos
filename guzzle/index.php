<?php
require '../vendor/autoload.php';

error_reporting(-1);

use _2UpMedia\HookyDemo\Guzzle\CustomClient as Client;

/**
 * @param $response
 */
function getResponseBody($response)
{
    if ($response instanceof \React\Promise\PromiseInterface) {
        $response->then(function ($response) {
            /***
             * @var $response DOMDocument
             */
            $domXpath = new \DOMXPath($response);

            $nodeList = $domXpath->query("/*//*[local-name()='entry']");

            $count = $nodeList->length;

            echo "There's $count entries on www.stackapps.com/feeds", "\n";
        });
    } else {
        $html = (string) $response->getBody();
        echo "HTML for www.google.com:\n", $html;
    }
}

Client::globalAfterGetHook(function($url, $options, $originalReturn){
    // return DOMDocument if the client response is XML

    /**
     * @param $response
     * @param $originalReturn
     * @return DOMDocument
     */
    $returnDOMDocument = function ($response, $originalReturn)
        {
            $body = (string) $response->getBody();

            if (substr(ltrim($body), 0, 5) == '<?xml') {
                $document = new \DOMDocument();
                $document->loadXML($body);

                return $document;
            } else {
                return $originalReturn;
            }
        };

    if ($originalReturn instanceof \GuzzleHttp\Message\FutureResponse) {
        return $originalReturn->then(function ($response) use ($originalReturn, $returnDOMDocument) {
            return $returnDOMDocument($response, $originalReturn);
        });
    } else {
        return $returnDOMDocument($originalReturn, $originalReturn);
    }
});

$client = new Client();

$response = $client->get('www.stackapps.com/feeds');
getResponseBody($response);

$response = $client->get('www.google.com');
getResponseBody($response);
