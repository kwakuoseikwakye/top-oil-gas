<?php

namespace App\Http\Resources;

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
        return [
            "name" => "{$this->fname} {$this->mname} {$this->lname}",
            "phone" => $this->phone,
            "size" => $this->cylinder_size,
            "cylcode" => $this->cylcode,
            "vendor" => $this->vendor_no,
            "id" => $this->transid,
            "location" => $this->location,
            "date" => date('jS M Y', strtotime($this->createdate)) . ' - ' . date('H:i:s A', strtotime($this->createdate)),
            "action" => " 
                <button type='button' {$disabled} data-row-transid='$this->transid'
                    rel='tooltip' class='btn {$class} btn-sm return-btn'>
                    {$name}
                </button>
                <button type='button' {$disabled} data-row-transid='$this->transid'
                    rel='tooltip' class='btn btn-danger btn-sm dispatch-delete-btn'>
                    Delete
                </button>
            ",

        ];
    }
}
