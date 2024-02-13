<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CylinderSizeResource extends JsonResource
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
            "transid" => $this->transid,
            "desc" => $this->description,
            "action" => "
            <button type='button' rel='tooltip' class='btn btn-success btn-sm size-edit-btn'>
            Edit
            </button>
            <button type='button' rel='tooltip' class='btn btn-danger btn-sm size-delete-btn'>
            Delete
            </button>
            "
        ];
    }
}
