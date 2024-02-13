<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseDispatchResource extends JsonResource
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
            "date" => date("jS M Y H:i:s",strtotime($this->createdate)),
            "fromwname" => $this->fromWarehouse->wname,
            "towname" => $this->toWarehouse->wname,
            "vname" => $this->warehouseUser->username,
            "cylcode" => $this->cylcode,
            "size" => $this->cylinder_size,
        ];
    }
}
