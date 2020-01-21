<?php
declare(strict_types=1);

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

final class ErrorTest extends \PHPUnit\Framework\TestCase
{
    public function tester()
    {
        echo "\n\n";
        $handlerStack = HandlerStack::create();
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->request('GET', 'http://localhost/testAdolfo/public/check/city=malaga');
        $body = $response->getBody();
        echo "check city by criteria:\n".$body."\n\n";
        $this->assertTrue($response !== false);


        $response = $client->request('GET', 'http://localhost/testAdolfo/public/criteria');
        $body = $response->getBody();
        echo "criteria list:\n".$body."\n\n";
        $this->assertTrue($response !== false);

        $response = $client->request('GET', 'http://localhost/testAdolfo/public/criteria/n=oddLetter&s=true');
        $body = $response->getBody();
        echo "enable(true)/disable(false) criterias:\n".$body."\n\n";
        $this->assertTrue($response !== false);

        $response = $client->request('GET', 'http://localhost/testAdolfo/public/criteria/new/n=test&o=weather&f=main&p=compare&v=clouds');
        $body = $response->getBody();
        echo "add new criteria by field existing pool:\n".$body;
        $this->assertTrue($response !== false);
        
    }
}
