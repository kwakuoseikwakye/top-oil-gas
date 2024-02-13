<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OutstandingResource extends JsonResource
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
            "name" => "{$this->fname} {$this->mname} {$this->lname}",
            "phone" => $this->phone,
            "size" => $this->cylinder_size,
            "cylcode" => $this->cylcode,
            "vendor" => $this->vendor_no,
            "id" => $this->transid,
            "location" => $this->location,
            "date" => date('jS M Y', strtotime($this->createdate)) . ' - ' . date('H:i:s A', strtotime($this->createdate)),
        ];
    }
}
