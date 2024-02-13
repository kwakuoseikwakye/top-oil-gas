<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Geocoder\Facades\Geocoder;

class ExchangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $cordinates = Geocoder::getAddressForCoordinates($this->latitude,$this->longitude);
        return [
            "customer" => "{$this->fname} {$this->mname} {$this->lname}",
            "vendor" => "{$this->vfname} {$this->vmname} {$this->vlname}",
            "cylcode_new" => $this->cylcode_new,
            "cylcode_old" => $this->cylcode_old,
            "barcode" => $this->barcode,
            "vendor_no" => $this->vendor_no,
            "custno" => $this->custno,
            "date" => date('jS F Y', strtotime($this->createdate)) ,
            // "address" => $cordinates['formatted_address'],
            "phone" => $this->customer_phone,
            "old_size" => $this->old_cylinder_size,
            "new_size" => $this->new_cylinder_size,
            "vendor_pics" => $this->vendor_pictures,
            "customer_pics" => $this->customer_pictures,
            "action" => "<button class='btn btn-info btn-sm view-btn'>View</button>"
        ];
    }
}

//boss level
//ice road
//below zero
//redemption day
//those who wished me dead
//chaos walking