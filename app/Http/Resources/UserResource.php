<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->fullName(),
            'email' => $this->email,
            'department_id' => $this->department_id,
            'department_code' => $this->whenLoaded('department', fn () => $this->department?->code),
            'department_name' => $this->whenLoaded('department', fn () => $this->department?->name),
            'role' => $this->whenLoaded('roles', fn () => $this->roles->first()?->name),
            'last_active_at' => $this->last_active_at,
            'created_at' => $this->created_at,
        ];
    }
}
