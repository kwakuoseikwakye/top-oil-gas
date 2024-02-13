<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerCylinderResource extends JsonResource
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
            // "vendor" => "{$this->vendor->fname} {$this->vendor->mname} {$this->vendor->lname}",
            "customer" => "{$this->customer->fname} {$this->customer->mname} {$this->customer->lname}",
            "vendor_code" => $this->vendor,
            "customer_code" => $this->custno,
            "barcode" => $this->barcode,
            "date" => date("jS M Y",strtotime($this->date_acquired)),
            "cylcode" => $this->cylcode,
            "status" => $this->status == 0 ? 'Returned' : 'Not returned',
            "transid" => $this->transid,
            "images" => $this->cylinder->images,
        ];
    }
}
