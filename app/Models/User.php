<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Observers\ActivityLogObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'library_id',
        'email',
        'locale',
        'password',
        'status',
        'profile_picture',
        'last_login_time',
        'deactivated_at',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'deactivated_at'=>'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale(): string
    {
        return $this->locale;
    }

    /**
     * Get the user's role.
     */
    public function role(): string
    {
        return $this->role;
    }

    public function suspend(){
        $this->status = 'inactive';
        $this->deactivated_at = Carbon::now();
        $this->save();
    }

   
    // protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => $this->{'name_' . app()->getLocale()},
    //     );
    // }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function library()
    {
        return $this->belongsTo(Library::class, 'library_id');
    }


    public function privilages()
    {
        return $this->belongsToMany(
            Privilage::class,
            'user_privilages',
            'user_id',
            'privilage_id'
        );
    }

    protected static function boot() {
        parent::boot();
        static::observe(ActivityLogObserver::class);
    }
}
