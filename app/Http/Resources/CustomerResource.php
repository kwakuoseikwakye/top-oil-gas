<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            "name" => "{$this->title} {$this->fname}",
            "title" => $this->title,
            "code" => $this->custno,
            "phone" => $this->phone,
            "email" => $this->email,
            "fname" => $this->fname,
            "mname" => $this->mname,
            "lname" => $this->lname,
            "region" => $this->region,
            "town" => $this->town,
            "lname" => $this->lname,
            "streetname" => $this->streetname,
            "landmark" => $this->landmark,
            "idtype" => $this->id_type,
            "idno" => $this->id_no,
            "gender" => $this->gender,
            "gender_lower" => strtolower($this->gender),
            "gps" => $this->gpsaddress,
            "long" => $this->longitude,
            "lat" => $this->latitude,
            "idimage" => $this->idFileLink,
            "picture" => $this->picture,
            "address" => $this->home_address,
            "occupation" => $this->occupation,
            "marital_status" => $this->marital_status,
            "pob" => $this->pob,
            "dob" => $this->dob,
        ];
    }
}
