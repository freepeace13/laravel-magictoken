<?php

namespace MagicToken\Events;

use MagicToken\DatabaseToken;
use Illuminate\Queue\SerializesModels;

class MagicTokenCreated
{
    use SerializesModels;

    /**
     * The magic token instance.
     *
     * @var \MagicToken\DatabaseToken
     */
    public $token;

    public $receiver;

    public $code;

    /**
     * Create a new instance of event.
     *
     * @param \MagicToken\DatabaseToken $token
     */
    public function __construct(DatabaseToken $token)
    {
        $this->receiver = $token->receiver;
        $this->code = $token->code;
        $this->token = $token->token;
    }
}
