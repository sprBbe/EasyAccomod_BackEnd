<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'detail_address' => $this->detail_address,
            'national_id_number' => $this->national_id_number,
            'id_role'=> $this->id_role,
            'role'=> $this->role->name,
            'phone' => $this->phone,
            'id_ward' => $this->id_ward,
            'ward' => isset($this->ward)? $this->ward->name: null,
            'district' => isset($this->ward)?$this->ward->district->name: null,
            'province' => isset($this->ward)?$this->ward->district->province->name: null,
        ];
    }
}
