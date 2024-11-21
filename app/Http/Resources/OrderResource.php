<?php

namespace App\Http\Resources;

use App\Models\CustomerCylinder;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->status == Orders::PENDING) {
            $status = "<span class='badge badge-warning'>{$this->status}</span>";
        } else if ($this->status == Orders::SUCCESS) {
            $status = "<span class='badge badge-success'>{$this->status}</span>";
        } else {
            $status = "<span class='badge badge-danger'>{$this->status}</span>";
        }

        if (empty($this->code)) {
            $new = "<span class='font-weight-bold text-success'>New Request</span>";
            $disabled = null;
        } else {
            $new = "<span class='font-weight-bold text-warning'>Cylinder Refil</span>";
            $disabled = 'disabled';
        }
        return [
            "transid" => $this->id,
            "order_id" => $this->order_number,
            "custno" => $this->customer_id,
            "customer" => "{$this->fname} {$this->lname}",
            "cylcode" => $this->code ?? "N/A",
            "weight_id" => $this->weight_id,
            "new" => $new,
            "date" => date('jS F Y H:i:s A', strtotime($this->date_acquired)),
            "weight" => "GHS {$this->amount} ({$this->weight})",
            "location" => "<b>Name</b> : {$this->name}\n
            <b>Address</b> : {$this->address}\n
            <b>Phone</b> : {$this->phone1} - {$this->phone2}\n
            <b>Additional Info</b> : {$this->additional_info}",
            "status" => $status,
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
                      class='dropdown-item btn btn-sm view-btn mt-2 assign-cylinder-btn' 
                      data-toggle='modal' {$disabled}
                      title=''>
                          Assign Cylinder
                  </button>
                  <button
                  class='dropdown-item btn btn-sm view-btn mt-2 refil-cylinder-btn' 
                  data-toggle='modal'
                  title=''>
                      Refill Cylinder
              </button>
            
                  </div>
              </div>
            "
        ];
    }
}
