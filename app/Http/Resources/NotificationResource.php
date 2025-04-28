<?php

namespace App\Http\Resources;

use App\Models\Business;
use App\Models\BusinessNotification;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            $isRead = false;
            
            // Check if notification has been read by the current user
            if ($request->user()) {
                $isRead = $this->isReadBy($request->user());
            }
            
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->content,
                'image' => $this->image ? Storage::url($this->image) : null,
                'created_at' => $this->created_at,
                'sent_at' => $this->sent_at,
                'is_read' => $isRead,
                'business' => BusinessResource::make($this->whenLoaded('business')),
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
        
        return [
            'name' => 'Unknown',
            'image' => null,
        ];
    }
}
