<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResponseResource extends JsonResource
{
    protected $status_code;

    public function __construct($message, $status_code = 200, $additional_data = [])
    {
        $this->status_code = $status_code;

        $data = [];
        $data['message'] = $message;    
        $data = array_merge($data, $additional_data);

        parent::__construct($data);
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->status_code);
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
