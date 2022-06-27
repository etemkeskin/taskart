<?php

namespace App\Http\Services;

class JsonResponseHandler
{
    private $status = false;
    private $message = null;
    private $error = null;
    private $error_type = null;
    private $data = null;
    
    // $response = [
    //     'status' => false,
    //     'error' => null,
    //     'errorType' => null,
    //     'data' => null
    // ];
    public function __construct(){   
       
    } 

    public function setStatus($status = false)
    {
        $this->status = $status;
    }

    public function setError($error = null)
    {
        $this->error = $error;
    }

    public function setErrorType($error_type = null)
    {
        $this->error_type = $error_type;
    }

    public function setMessage($message = null)
    {
        $this->message= $message;
    }

    public function setData($data = false)
    {
        $this->data = $data;
    }

    public function getJsonResponse(){
        $response['status'] = $this->status;
        $response['message'] = $this->message;
        $response['error'] = $this->error;
        $response['errorType'] = $this->error_type;
        $response['data'] = $this->data;
        return $response;
    }

}