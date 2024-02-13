<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            "code" => $this->route_code,
            "desc" => $this->route_description,
            "action" => "
            <button type='button' rel='tooltip' class='btn btn-success btn-sm loc-edit-btn'>
            Edit
            </button>
            <button type='button' rel='tooltip' class='btn btn-danger btn-sm loc-delete-btn'>
            Delete
            </button>
            "
        ];
    }
}
