<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'remaining_days' => $this->remaining_days,
            'roles'        => $this->whenLoaded(
                'roles',
                fn() =>
                $this->roles->map(fn($r) => [
                    'id'           => $r->id,
                    'name'         => $r->name,
                    'display_name' => $r->display_name,
                ])
            ),
            'permissions'  => $this->whenLoaded('roles', fn() => $this->permission_names),
            'role_name'    => $this->role_name,
            'role_display' => $this->role_display,

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
