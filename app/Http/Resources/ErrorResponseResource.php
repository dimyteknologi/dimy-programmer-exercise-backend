<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResponseResource extends JsonResource
{
    protected $error_status_code;
    public static $wrap = null;

    public function __construct($error_message, $error_status_code = 500, $additional_data = [])
    {
        $this->error_status_code = $error_status_code;

        $error_data = [];
        $error_data['message'] = $error_message;    
        $error_data = array_merge($error_data, $additional_data);

        parent::__construct($error_data);
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->error_status_code);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
