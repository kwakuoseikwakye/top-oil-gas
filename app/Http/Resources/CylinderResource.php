<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CylinderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            "transid" => $this->transid,
            "owner" => $this->owner,
            "barcode" => $this->barcode,
            "cylcode" => $this->cylcode,
            "size" => $this->size,
            "weight" => $this->weight_id,
            "notes" => $this->notes,
            "amount" => $this->initial_amount,
            "images" => $this->images,
        ];
    }
}
