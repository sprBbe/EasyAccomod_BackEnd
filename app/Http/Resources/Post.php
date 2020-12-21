<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Post extends JsonResource
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
            'title' => $this->title,
            'info_detail' => $this->info_detail,
            'detail_address' => $this->detail_address,
            'id_ward' => $this->id_ward,
            'ward' => $this->ward->name,
            'district' => $this->ward->district->name,
            'province' => $this->ward->district->province->name,
            'with_owner' => $this->with_owner,
            'restroom' => $this->restroom,
            'kitchen' => $this->kitchen,
            'water_heater' => $this->water_heater,
            'air_conditioner' => $this->air_conditioner,
            'balcony' => $this->balcony,
            'additional_amenity' => $this->amenities,
            'near_place' => $this->nearPlaces,
            'id_room_type' => $this->id_room_type,
            'square' => $this->square,
            'price' => $this->price,
            'electricity_price' => $this->electricity_price,
            'water_price' => $this->water_price,
            'time_expire' => $this->time_expire,
            'imgs' => $this->images,
            'status' => $this->status,
            'views' => $this->views,
            'favourites' => $this->favUsers->count(),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
