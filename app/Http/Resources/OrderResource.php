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
        if ($this->status == CustomerCylinder::PENDING) {
            $status = "<span class='badge badge-warning'>{$this->status}</span>";
        } else if ($this->status == CustomerCylinder::SUCCESS) {
            $status = "<span class='badge badge-success'>{$this->status}</span>";
        } else {
            $status = "<span class='badge badge-danger'>{$this->status}</span>";
        }
        return [
            "order_id" => $this->order_id,
            "customer" => "{$this->fname} {$this->lname}",
            "cylcode" => $this->cylcode,
            "date" => date('jS F Y H:i:s A', strtotime($this->date_acquired)),
            "weight" => "GHS {$this->amount} - {$this->weight}",
            "location" => "<b>Name</b> : {$this->name}\n
            <b>Address</b> : {$this->address}\n
            <b>Phone</b> : {$this->phone1} - {$this->phone2}\n
            <b>Additional Info</b> : {$this->additional_info}",
            "status" => $status,
        ];
    }
}
