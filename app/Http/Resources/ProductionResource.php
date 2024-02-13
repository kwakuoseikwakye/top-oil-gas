<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
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
            "staff" => "{$this->fname} {$this->mname} {$this->lname}",
            "cylcode_new" => $this->cylcode_new,
            "cylcode_old" => $this->cylcode_old,
            "empty" => $this->weight_empty,
            "full" => $this->weight_filled,
            "total" => $this->total_weight,
            "action" => "
                <button class='btn btn-primary btn-md'>Fill Cylinder</button>
            "
        ];
    }
}
