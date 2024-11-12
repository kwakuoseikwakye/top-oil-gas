<?php

namespace App\Http\Resources;

use App\Models\CustomerLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $location = CustomerLocation::where('default', 1)->where('customer_id', $this->id)->first();
        $address = $location->address ?? "n/a";
        $name = $location->name ?? "n/a";
        $phone = $location->phone1 ?? "n/a";
        $info = $location->additional_info ?? "n/a";
        return [
            "id" => $this->id,
            "name" => "{$this->fname} {$this->lname}",
            "phone" => $this->phone,
            "fname" => $this->fname,
            "mname" => $this->mname,
            "lname" => $this->lname,
            "idtype" => $this->id_type,
            "idno" => $this->id_no,
            "idimage" => $this->id_link,
            "picture" => $this->picture,
            "address" => "<b>Name</b> : {$name}\n
            <b>Address</b> : {$address}\n
            <b>Contact Phone</b> : {$phone}\n
            <b>Additional Info.</b> : {$this->info}",
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
