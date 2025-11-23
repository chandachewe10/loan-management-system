<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWallets;
use Bavix\Wallet\Interfaces\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class User extends Authenticatable implements Wallet,MustVerifyEmail,HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasWallet, HasWallets;
    use HasRoles;
    use HasPanelShield;
    use LogsActivity;
    use InteractsWithMedia;



    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id','id');
    }

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logExcept(['password']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'branch_id',
        'company_representative',
        'company_representative_phone',
        'company_representative_email',
        'company_phone',
        'company_address',
        'profile_completion_modal_shown'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


protected static function booted(): void
    {
        static::addGlobalScope('org', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('organization_id', auth()->user()->organization_id)
               // ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);

            }
        });
    }

    /**
     * Calculate profile completeness percentage
     */
    public function getProfileCompletenessAttribute(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'company_representative' => !empty($this->company_representative),
            'company_representative_phone' => !empty($this->company_representative_phone),
            'company_representative_email' => !empty($this->company_representative_email),
            'company_phone' => !empty($this->company_phone),
            'company_address' => !empty($this->company_address),
            'company_logo' => $this->hasMedia('company_logo'),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if profile is incomplete
     */
    public function isProfileIncomplete(): bool
    {
        return $this->profile_completeness < 100;
    }

}
