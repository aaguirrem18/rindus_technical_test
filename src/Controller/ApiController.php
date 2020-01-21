<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\WeatherApi;
use App\Controller\CriteriaController;



class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */

     
    // this will be replace for sql object
    private $src = "../src/criteria.json";

    public function index()
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    public function checkProcess($city)
    {

        $conn = new WeatherApi();
        $weather_response = $conn->connectionApi($city);
 
        if( $weather_response['error'] == FALSE ){

            $pool_response = array();
            $criteriaList = json_decode(file_get_contents($this->src),true);

            //check oddLetter
            //validate enable status of criteria

            if( $criteriaList['oddLetter']['enable'] == 'true' ){
                $oddLetter = ( strlen($city) & 1 ) ? true : false;
                $pool_response['oddLetter'] = $oddLetter;
            }
            
            //check dayTemp
            $dayTemp  = false;

            //validate enable status of criteria
            if( $criteriaList['dayTemp']['enable']  == 'true' ){
                $current_temp = ceil($weather_response['content']['main']['temp']-273.15);
                $current_time = date("H:i a");
                $sunrise = date("H:i a ", $weather_response['content']['sys']['sunrise']);
                $sunset = date("H:i a", $weather_response['content']['sys']['sunset']);
                if ($current_time > $sunrise && $current_time < $sunset){
                    if ($current_temp > 17 && $current_temp < 25) {
                            //it is currently night (between sunset and sunrise)
                            //the temperature is between 10 and 15 degrees Celcius
                            $dayTemp = true;
                    }
                }else{
                    if ($current_temp > 10 && $current_temp < 15) {
                        //it is daytime and the temperature is between 17 and 25 degrees Celcius.
                        $dayTemp = true;
                    }
                }
                $pool_response['dayTemp'] = $dayTemp;
            }

            /*
            * rival
            * validate enable status of criteria
            */
            if( $criteriaList['rival']['enable']  == 'true' ){
                $conn = new WeatherApi();
                $rivalCheck = $conn->rivalCheck();
                $rival = ( $current_temp > $rivalCheck['temp'] ) ? true : false;
                $pool_response['rival'] = $rival;
            }

            /*
            * The output is an AND expresion of all criteria (so true if all criteria are met) 
            * as well as a list of all criterion and their Boolean status.
            */
            $check = in_array(false,$pool_response) ? false : true ;
            $data_array = array('city'=>$weather_response['content']['name'],'check'=>$check,'criteria'=>$pool_response,'error'=>false);
        }else{

            /* 
            * Return in case of an error JSON string error=true
            */
            $data_array = array('city' =>$weather_response['content']['name'],'check' => 'NOT FOUND','error' => true);
        }

        $response = new JsonResponse($data_array);
        $response->headers->set('Content-Type','application/json');

        return $response;
    }

    public function checkNewCriterias()
    {
        /*
        * check criterias added
        */
        return $data_array;
    }

    
}
