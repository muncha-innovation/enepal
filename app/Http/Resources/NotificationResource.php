<?php

namespace App\Http\Resources;

use App\Events\MemberAddedToBusiness;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = collect($this->data);
        // dd($data->get('created_by_type'));
        $creatorDetails = $this->creatorDetails();
        return [
            'id' => $this->id,
            'title' => $data->get('title'),
            'description' => $data->get('message'),
            'image' => $data->get('image'),
            'createdByType' => $data->get('created_by_type'),
            'createdById' => $data->get('created_by_id'),
            'createdByImage' => $creatorDetails['image'],
            'createdByName' => $creatorDetails['name'],
        ];
    }

    public function creatorDetails() {
        $data = collect($this->data);
        if($data->get('created_by_type') == 'business') {
            $business = Business::find($data->get('created_by_id'));
            return [
                'name' => $business->name,
                'image' => getImage($business->logo, 'business/logo/'),
            ];
        } else if($data->get('created_by_type') == 'user'){
            $user = User::find($data->get('created_by_id'));
            return [
                'name' => $user->name,
                'image' => getImage($user->image, 'profile/'),
            ];
        }
    }

   


}
