<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "transid" => $this->transid,
            "wcode" => $this->wcode,
            "wname" => $this->wname,
            "phone" => $this->phone,
            "email" => $this->email,
            "region" => $this->region,
            "town" => $this->town,
            "streetname" => $this->streetname,
            "landmark" => $this->landmark,
            "gpsaddress" => $this->gpsaddress,
        ];
    }
}
