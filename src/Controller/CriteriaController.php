<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CriteriaController extends AbstractController
{
    /**
     * @Route("/criteria", name="criteria")
     */

    // this will be replace for sql object
    private $src = "../src/criteria.json";
    private $src_weather = "../src/weather_api_fields.json";


    public function index()
    {
        return $this->render('criteria/index.html.twig', [
            'controller_name' => 'CriteriaController',
        ]);
    }

    public function criteria()
    {
        $criteriaList = $this->parseCriteriaList();
        return $criteriaList;
    }

    public function criteriaList()
    {
        $criteriaList = $this->parseCriteriaList();
        $response = new JsonResponse($criteriaList);
        return $response;
    }

    public function criteriaConfig($name,$status)
    {
        $criteriaList = $this->parseCriteriaList();

        if( array_key_exists ($name, $criteriaList) ){
            $criteriaList[$name]['enable'] = $status;
            file_put_contents($this->src, json_encode($criteriaList));
        }else{
            $criteriaList = array('error' => true,'criteria' => 'criteria not Found');
        }

        $response = new JsonResponse($criteriaList);
        //$response->headers->set('Content-Type','application/json');
        return $response;
    }

    public function criteriaAdd($name, $option, $field, $process, $value)
    {
        $new = false;
        $field_list = json_decode(file_get_contents($this->src_weather),true);


        /* 
        * Validate criteria pool fields
        *
        * exepected json: 
        * "name": {
        *     "description": "",
        *     "fields":
        *        "option": "field"
        *      },
        *      "process" : "",
        *      "value" : "",
        *      "enable": "true"
        *  }
        *
        * Return in case of an error JSON string error=true
        */

        if( array_key_exists( $option,$field_list['pool_field']) ){
            if(array_key_exists( $field,$field_list['pool_field'])){
                if( array_key_exists( $process,$field_list['pool_field'][$option]['functions']) ){
                    $new = true;
                }else{
                    $newCriteria = array('error' => true,'process' => 'process not Found');
                }
            }else{
                $newCriteria = array('error' => true,'field' => 'field not Found');
            }
        }else{
            $newCriteria = array('error' => true,'option' => 'option not Found');
        }

        if($new){

            $newCriteria = array( 
                "description" => "",
                "fields" => array($option => $field),
                "process" => $process,
                "value" => $value,
                "enable" => "true"
            
            );

            $criteriaList = json_decode(file_get_contents($this->src),true);
            $criteriaList[$name] = $newCriteria;
            file_put_contents($this->src, json_encode($criteriaList));
            $newCriteria = array('error' => false,'criteria' => 'NEW criteria added');
        }
     
        $response = new JsonResponse($newCriteria);
        return $response;
    }

    public function parseCriteriaList()
    {
        $criteriaList = json_decode(file_get_contents($this->src),true);
        return $criteriaList;

    }

}
