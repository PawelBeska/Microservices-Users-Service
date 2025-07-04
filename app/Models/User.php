<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory;
    use Notifiable;
    use HasUuids;

    protected $guarded = [
        'id'
    ];


    protected $hidden = [
        'password',
        'remember_token',

    ];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'user' => [
                #!TODO add roles and permissions
                'roles' => [
                    'test',
                    'admin',
                ],
                'permissions' => [],
                'service' => [
                    'name' => config('microservices.name'),
                    'host' => config('microservices.host'),
                    'port' => config('microservices.port'),
                    'service' => config('microservices.service'),
                ],
                ...$this->toArray()
            ],
        ];
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
