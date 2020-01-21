<?php

namespace App\Service;
use Symfony\Component\HttpClient\HttpClient;

class WeatherApi
{
    // this will be replace for sql object
    private $src = "../src/criteria.json";

    public function connectionApi(string $city): array
    {
        /*
        * Tested with Postman
        */ 
        $client = HttpClient::create();

        try {
            $response = $client->request('GET', 'http://api.openweathermap.org/data/2.5/weather?', [
                // these values are automatically encoded before including them in the URL
                'query' => [
                    'q' => $city,
                    'appid' => '8ca1bf554fe26dff41d635d4e2f866ed',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = json_decode($content ,true);

            $status = array('error' => false,'content' => $content);
        } catch (\Throwable $th) {
            /* 
            * Return in case of an error JSON string error=true
            */
            $status = array('error' => true,'content' => 'Not Found');
        }

        return $status;
    }

    //check rival criteria
    public function rivalCheck(): array
    {
        $criteria = json_decode(file_get_contents($this->src),true); //criteria.json
        $rival_content = $this->connectionApi($criteria['rival']['city']); //Köln
        $rival_temp = array(
            'city' => $rival_content['content']['name'],
            'temp' => ceil($rival_content['content']['main']['temp']-273.15),
        );

        return $rival_temp;
    }

    
}