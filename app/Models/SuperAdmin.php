<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property string $full_name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin whereUserId($value)
 * @mixin \Eloquent
 */
class SuperAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function (SuperAdmin $SuperAdmin) {
            $SuperAdmin->user->update([
                'role' => 'superadmin',
            ]);
            static::deleting(function ($model) {
            if ($model->user) {
                $model->user->delete();
            }
        });
        });
    }
}