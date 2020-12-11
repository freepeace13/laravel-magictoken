<?php

namespace MagicToken;

use Carbon\Carbon;
use MagicToken\Action;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use MagicToken\Events\MagicTokenCreated;
use MagicToken\Exceptions\InvalidTokenException;

class MagicTokenRepository
{
    protected $model;

    protected $length;

    protected $hashKey;

    protected $expires;

    protected $maxTries;

    public function __construct($model, $hashKey, $length = 6, $maxTries = 3, $expires = 60)
    {
        $this->model = $model;
        $this->hashKey = $hashKey;
        $this->length = $length;
        $this->expires = $expires * 60;
        $this->maxTries = $maxTries;
    }

    public function create(Action $action, $receiver, $maxTries = null)
    {
        $instance = new $this->model;

        $instance->action = $action;
        $instance->receiver = $receiver;
        $instance->max_tries = $maxTries ?? $this->maxTries;

        $instance->token = $this->createNewToken();
        $instance->code = $this->createNewCode();

        $instance->save();

        Event::dispatch(new MagicTokenCreated($instance));

        return $instance->token;
    }

    public function renew($token)
    {
        $existing = $this->model::where('token', $token)->firstOrFail();

        $newToken = $this->create(
            $existing->action,
            $existing->receiver,
            $existing->max_tries
        );

        $this->delete($existing->token);

        return $newToken;
    }

    public function attempt($token, $pincode)
    {
        if (!($record = $this->find($token))) {
            return false;
        }

        if ($record->num_tries > $record->max_tries) {
            throw new InvalidTokenException('Token has reached the maximum attempts.');
        }

        if ((string) $record->code !== (string) $pincode) {
            $record->increment('num_tries');

            return false;
        }

        $this->delete($record->token);

        return $record->action;
    }

    public function delete($token)
    {
        return $this->model::where('token', $token)->delete();
    }

    public function exists($token)
    {
        return $this->valid($this->find($token));
    }

    protected function find($token)
    {
        return $this->model::where('token', $token)->first();
    }

    protected function valid(DatabaseToken $record)
    {
        return $record
            && ! $this->tokenExpired($record)
            && ! $this->tokenLocked($record);
    }

    protected function tokenLocked(DatabaseToken $record)
    {
        return $record->num_tries <= $this->maxTries;
    }

    protected function tokenExpired(DatabaseToken $record)
    {
        return Carbon::parse($record->created_at)
            ->addSeconds($this->expires)
            ->isPast();
    }

    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    protected function createNewCode()
    {
        $mininum = array_fill(0, $this->length, 1);
        $maximum = array_fill(0, $this->length, 9);

        return mt_rand(join('', $mininum), join('', $maximum));
    }
}
