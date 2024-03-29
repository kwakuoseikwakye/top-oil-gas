<?php

namespace App\Http\Resources;

use App\Models\Dispatch;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->dispatch === "1") {
            $class = "btn-danger";
            $disabled = "disabled";
            $name = "Returned";
        } else {
            $disabled = "";
            $class = "btn-success";
            $name = "Return Cylinder";
        }

        if ($this->status == Dispatch::PENDING) {
            $status = "<span class='badge badge-warning'>{$this->status}</span>";
        } else if ($this->status == Dispatch::EN_ROUTE) {
            $status = "<span class='badge badge-success'>{$this->status}</span>";
        } else {
            $status = "<span class='badge badge-info'>{$this->status}</span>";
        }

        if (empty($this->pickup_location)) {
            $new = "<span class='font-weight-bold text-success'>CUSTOMER DELIVERY</span>";
        } else {
            $new = "<span class='font-weight-bold text-info'>CUSTOMER PICKUP</span>";
        }

        return [
            "name" => "{$this->fname} {$this->mname} {$this->lname}",
            "phone" => $this->phone,
            "delivery" => $new,
            "order_id" => $this->order_id,
            "pickup" => $this->pickup_location,
            "status" => $status,
            "vendor" => $this->vendor_no,
            "id" => $this->transid,
            "location_id" => $this->location_id,
            "location" => "<b>Name</b> : {$this->name}\n
            <b>Address</b> : {$this->address}\n
            <b>Phone</b> : {$this->phone1} - {$this->phone2}\n
            <b>Additional Info</b> : {$this->additional_info}",

            "pickup" => "<b>Name</b> : {$this->name}\n
            <b>Address</b> : {$this->address}\n
            <b>Contact Info</b> : {$this->contact_info}\n
            <b>Opening Hours</b> : {$this->opening_hours}",
            "date" => date('jS M Y', strtotime($this->createdate)) . ' - ' . date('H:i:s A', strtotime($this->createdate)),
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
                      class='dropdown-item btn btn-sm view-btn mt-2 assign-rider-btn' 
                      data-toggle='modal' {$disabled}
                      title=''>
                          Assign Rider
                  </button>
            
                  </div>
              </div>
            "

        ];
    }
}
