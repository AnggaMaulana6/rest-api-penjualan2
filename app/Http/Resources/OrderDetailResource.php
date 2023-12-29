<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => $this->whenLoaded('product'),
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer'),
            'quantity' => $this->quantity,
            'created_at' => date_format($this->created_at, "Y-m-d H:i:S"),
        ]);
    }
}
