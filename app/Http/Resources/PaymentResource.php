<?php

namespace App\Http\Resources;

use App\Models\Payment;
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
        if ($this->status == Payment::PENDING) {
            $status = "<span class='badge badge-warning'>{$this->status}</span>";
        } else if ($this->status == Payment::SUCCESS) {
            $status = "<span class='badge badge-success'>{$this->status}</span>";
        } else {
            $status = "<span class='badge badge-danger'>{$this->status}</span>";
        }
        return [
            "name" => "{$this->fname} {$this->lname}",
            "mode" => $this->payment_mode,
            "order_id" => $this->order_id,
            "transaction" => $this->transaction_id,
            "amount" => $this->amount_paid,
            "status" => $status,
        ];
    }
}
