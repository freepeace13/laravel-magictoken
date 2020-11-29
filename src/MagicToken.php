<?php

namespace MagicToken;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MagicToken\Events\TokenCreated;
use MagicToken\Events\TokenVerified;
use Illuminate\Support\Facades\URL;
use MagicToken\Contracts\Action;
use MagicToken\Exceptions\InvalidTokenException;

class MagicToken extends Model
{
    /**
     * Casts specified columns
     *
     * @var array
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $length = config('verifytoken.token.length', 6);

            $model->token = Str::random(64);
            $model->max_tries = config('magictoken.max_tries', 3);
            $model->code = join('', Arr::random(range(0, 9), $length));
        });
    }

    /**
     * Create new actionable token with the given options.
     *
     * @param MagicToken\Contracts\Action $action
     * @param integer $expires
     * @param integer $maxTries
     *
     * @return self
     */
    public static function create(Action $action, $expires = 5)
    {
        $instance = new static;
        $instance->action = $action;
        $instance->expires_at = Carbon::now()->addMinutes($expires);

        $instance->save();

        Event::dispatch(new TokenCreated($instance));

        return $instance;
    }

    /**
     * Scope a query that only include tokens that expired/unexpired.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param boolean $expired
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired(Builder $builder, bool $expired = true)
    {
        return $builder->where('expires_at', $expired ? '<' : '>', Carbon::now());
    }

    /**
     * Scope a query that only include tokens that are retriable.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetriable(Builder $builder)
    {
        return $builder->whereColumn('num_tries', '<=', 'max_tries');
    }

    /**
     * Get the signed token's url.
     *
     * @return string
     */
     public function getUrlAttribute()
     {
         return URL::signedRoute('magictoken.verify', ['token' => $this->token]);
     }

    /**
     * Unserialize action attribute value on get.
     *
     * @param  mixed  $value
     * @return \MagicToken\ActionInterface
     */
    public function getActionAttribute($value)
    {
        return $this->getConnection()->getDriverName() === 'pgsql'
            ? unserialize(base64_decode($value))
            : unserialize($value);
    }

    /**
     * Serialize action attribute value on set.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setActionAttribute($value)
    {
        $this->attributes['action'] = $this->getConnection()->getDriverName() === 'pgsql'
            ? base64_encode(serialize($value))
            : serialize($value);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('magictoken.database.table_name');
    }
}
