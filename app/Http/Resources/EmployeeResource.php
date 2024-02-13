<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            "code" => $this->staffid,
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
            // "gender_lower" => strtolower($this->gender),
            "gps" => $this->gpsaddress,
            "empdate" => $this->empdate,
            "dob" => $this->dob,
        ];
    }
}
