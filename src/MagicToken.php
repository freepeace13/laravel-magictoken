<?php

namespace MagicToken;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MagicToken\Events\MagicTokenCreated;
use MagicToken\Events\MagicTokenVerified;

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
            $model->code = join('', Arr::random(range(0, 9), $length));
        });
    }

    /**
     * Create new actionable token with the given options.
     *
     * @param MagicToken\ActionInterface $action
     * @param integer $expires
     * @param integer $maxTries
     *
     * @return self
     */
    public static function create(ActionInterface $action, $expires = 5, $maxTries = 3)
    {
        $instance = new static;

        $instance->action = $action;
        $instance->max_tries = $maxTries;
        $instance->expires_at = Carbon::now()->addMinutes($expires);

        $instance->save();

        Event::dispatch(new MagicTokenCreated($instance));

        return $instance;
    }

    /**
     * Find pending token that throws an exception when not found.
     *
     * @param mixed $token
     * @return self
     *
     * @throws MagicToken\InvalidTokenException
     */
    public static function findValidToken($token)
    {
        $existing = self::findPendingToken($token);

        if (is_null($existing)) {
            throw new InvalidTokenException('Token expired or deleted.');
        }

        return $existing;
    }

    /**
     * Find unexpired, unverified and retriable token record.
     *
     * @param mixed $token
     * @return self
     */
    public static function findPendingToken($token)
    {
        return self::expired(false)
            ->retriable()
            ->whereToken($token)
            ->whereNull('verified_at')
            ->first();
    }

    /**
     * Delete all existing expired tokens.
     *
     * @return bool
     */
    public static function deleteExpiredTokens()
    {
        return self::expired(true)->delete();
    }

    /**
     * Mark token as verified and dispatches verified event.
     *
     * @return self
     */
    public function markVerified()
    {
        $this->forceFill([
            'verified_at' => Carbon::now()
        ])->save();

        Event::dispatch(new MagicTokenVerified($this));

        return $this;
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
