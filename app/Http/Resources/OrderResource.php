<?php

namespace App\Http\Resources;

use App\Models\CustomerCylinder;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status == CustomerCylinder::PENDING_PAYMENT) {
            $payLinkDisabled = null;
            $status = "<span class='badge badge-info'>{$this->status}</span>";
        } else if ($this->status == CustomerCylinder::PENDING_ASSIGNMENT) {
            $payLinkDisabled = 'disabled';
            $status = "<span class='badge badge-warning'>{$this->status}</span>";
        } else if ($this->status == CustomerCylinder::SUCCESS) {
            $payLinkDisabled = 'disabled';
            $status = "<span class='badge badge-success'>{$this->status}</span>";
        } else {
            $payLinkDisabled = 'disabled';
            $status = "<span class='badge badge-danger'>Invalid Order</span>";
        }

        if (empty($this->cylcode)) {
            $new = "<span class='font-weight-bold text-success'>New Request</span>";
            $disabled = null;
            $refillDisabled = 'disabled';
        } else {
            $new = "<span class='font-weight-bold text-warning'>Cylinder Refil</span>";
            $disabled = 'disabled';
            $refillDisabled = null;

        }
        return [
            "transid" => $this->transid,
            "order_id" => $this->order_id,
            "custno" => $this->custno,
            "customer" => "{$this->fname} {$this->lname}",
            "cylcode" => $this->cylcode ?? "N/A",
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
                data-toggle='modal' {$refillDisabled}
                title=''>
                    Refill Cylinder
                </button>
                <button
                class='dropdown-item btn btn-sm view-btn mt-2 send-payment-btn' 
                data-toggle='modal' {$payLinkDisabled}
                title=''>
                    Send Payment Link
                </button>
                <button
                class='dropdown-item btn btn-sm view-btn mt-2 collect-payment-btn' 
                data-toggle='modal' {$payLinkDisabled}
                title=''>
                    Collect Cash Payment
                </button>
            
                  </div>
              </div>
            "
        ];
    }
}
