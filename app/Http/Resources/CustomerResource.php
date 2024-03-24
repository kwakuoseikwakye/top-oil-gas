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
            "transid" => $this->id,
            "name" => "{$this->fname} {$this->lname}",
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
            "action" => "
            <div class='dropdown'>
                  <button
                      class='btn btn-sm dropdown-toggle'
                      type='button'
                      id='dropdownMenu2'
                      data-toggle='dropdown'
                      aria-haspopup='true'
                      aria-expanded='false'>
                      <i class='fas fa-bars'></i>
                  </button>
  
                  <div class='dropdown-menu' aria-labelledby='actionMenuDropdown'>
                  <button
                  class='dropdown-item btn btn-sm mt-2 location-btn' 
                  title=''>
                      Add location
                  </button>
                  <button
                      class='dropdown-item btn btn-sm mt-2 view-btn' 
                      title=''>
                          Customer Info
                  </button>
                  <button
                      class='dropdown-item btn btn-sm mt-2 edit-btn' 
                      title=''>
                          Edit
                  </button>
                  <button
                      class='dropdown-item btn btn-sm mt-2 delete-btn' 
                      title=''>
                          Delete
                  </button>
            
                  </div>
              </div>
            "
        ];
    }
}
