<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    const AUTH_TOKEN_TYPE = 'bearer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    /**
     * Get expires auth token (format used is seconds)
     *
     * @return string
     */
    public static function getTokenExpires()
    {
        return (Auth::factory()->getTTL() * 60);
    }

    public function saveModel(array $data)
    {
        try {
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->password = \Illuminate\Support\Facades\Hash::make($data['password']);

            if ($this->save()) {
                if (isset($data['permissions']) && count($data['permissions']) > 0) {
                    foreach ($data['permissions'] as $permission) {
                        $this->assignPermission([$permission]);
                    }
                }

                return true;
            }

            return false;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /* 
     * The original method name was getModel.
     * But the name must be changed because it was conflict with HasRoles class
     */
    public static function getModelData($params, $raw = false)
    {
        $modelQuery = static::query();

        if ( ($filter_name = Arr::get($params, 'name', false)) ) {
            $modelQuery->where('name', 'LIKE', '%' . $filter_name . '%');
        }

        if ( ($filter_email = Arr::get($params, 'email', false)) ) {
            $modelQuery->where('email', 'LIKE', '%' . $filter_email . '%');
        }

        if ( ($sort_data = Arr::get($params, 'sort', false)) !== false ) {
            $sort_pattern = \App\Helpers::parseSortingPattern($sort_data);
            $modelQuery->orderBy($sort_pattern['column'], $sort_pattern['order']);
        }

        if (!$raw) {
            if ( ($item_per_page = Arr::get($params, 'limit', false)) ) {
                $modelQuery = $modelQuery->paginate($item_per_page);
            } else {
                $modelQuery = $modelQuery->get();
            }
        }

        return $modelQuery;
    }

    public function assignPermission(array $ids, $guard = 'api')
    {
        try {
            $permissions = Permission::whereIn('id', $ids)->where('guard_name', $guard)->get();
            $this->givePermissionTo($permissions);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getPermissionLists($params = [], $raw = false)
    {
        $model = Permission::where($params);
        return $raw ? $model : $model->get();
    }
}
