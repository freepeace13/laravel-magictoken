<?php

namespace MagicToken;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MagicToken\Events\MagicTokenCreated;
use MagicToken\Contracts\Action;
use MagicToken\Exceptions\InvalidTokenException;

class DatabaseToken extends Model
{
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
