<?php

namespace App\Models;

use App\Http\Traits\CustomModelLogic;
use App\Models\Catalogs\CatProfile;
use App\Models\Catalogs\CatRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Eloquent;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property int $cat_profile_id
 * @property bool $is_active
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCatProfileId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsActive($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 * @property bool|null $is_logged_in
 * @method static Builder|User whereIsLoggedIn($value)
 * @property-read string $hash_id
 * @property-read CatProfile $profile
 * @method static Builder|User search($search)
 * @property-read int|null $projections_count
 * @property-read int|null $requests_count
 * @property-read int|null $status_count
 * @property-read int|null $dependents_count
 * @property string|null $name
 * @method static Builder|User whereName($value)
 * @property-read int|null $last_status_count
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CustomModelLogic;

    protected $appends = ['hash_id'];
    protected $fillable = [
        'username',
        'name',
        'password',
        'cat_profile_id',
    ];
    protected $hidden = [
        'password',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(CatProfile::class, 'cat_profile_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            CatRole::class,
            'user_roles',
            'user_id',
            'cat_role_id'
        );
    }
}
