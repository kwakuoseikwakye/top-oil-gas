<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            "mode" => $this->payment_mode,
            "cylcode" => $this->cylcode,
            "barcode" => $this->barcode,
            "amount" => $this->amount_paid,
        ];
    }
}
