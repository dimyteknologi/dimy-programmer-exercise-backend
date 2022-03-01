<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Spatie\Permission\Traits\HasRoles;

class Employees extends Model
{
    use HasFactory, Notifiable, HasRoles;

    const AUTH_TOKEN_TYPE = 'bearer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'tbl_employees';

    protected $fillable = [
        'employee_type_id',
        'name',
        'phone',
    ];

    public function salary(){
        return $this->hasMany(Salaries::class, 'employee_id', 'id');
    }

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
            $this->employee_type_id = $data['employee_type_id'];
            $this->phone = $data['phone'];

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

        if (($filter_name = Arr::get($params, 'name', false))) {
            $modelQuery->where('name', 'LIKE', '%' . $filter_name . '%');
        }


        if (($sort_data = Arr::get($params, 'sort', false)) !== false) {
            $sort_pattern = \App\Helpers::parseSortingPattern($sort_data);
            $modelQuery->orderBy($sort_pattern['column'], $sort_pattern['order']);
        }

        if (!$raw) {
            if (($item_per_page = Arr::get($params, 'limit', false))) {
                $modelQuery = $modelQuery->paginate($item_per_page);
            } else {
                $modelQuery = $modelQuery->get();
            }
        }

        return $modelQuery;
    }
}
