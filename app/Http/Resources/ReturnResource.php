<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnResource extends JsonResource
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
            "wname" => $this->wname,
            "empty_full" => $this->empty_full,
            "vendor" => $this->vendor_no,
            // "staff" => $this->staffid,
            "date" => date('jS M Y',strtotime($this->createdate)) . ' ' . date('H:i:s',strtotime($this->createdate)),

        ];
    }
}
