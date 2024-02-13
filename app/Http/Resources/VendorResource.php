<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            "code" => $this->vendor_no,
            "name" => "{$this->fname} {$this->mname} {$this->lname}",
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
            "gender_lower" => strtolower($this->gender),
            "idno" => $this->id_no,
            "gender" => $this->gender,
            "long" => $this->longitude,
            "lat" => $this->latitude,
            "gpsaddress" => $this->gpsaddress,
            "idimage" => $this->id_file_link,
            "picture" => $this->picture,
        ];
    }
}
