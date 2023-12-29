<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            'product_name' => $this->product_name,
            'price' => $this->id,
            'stock' => $this->id,
            'created_at' => date_format($this->created_at, "Y-m-d H:i:S"),
            'seller' => $this->whenLoaded('seller'),
            'orders' => $this->whenLoaded('orders'),
        ]);
    }
}
