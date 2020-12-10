<?php

namespace MagicToken\Events;

use MagicToken\MagicToken;
use Illuminate\Queue\SerializesModels;

class MagicTokenCreated
{
    use SerializesModels;

    /**
     * The magic token instance.
     *
     * @var \MagicToken\MagicToken
     */
    public $token;

    /**
     * Create a new instance of event.
     *
     * @param \MagicToken\MagicToken $token
     */
    public function __construct(MagicToken $token)
    {
        $this->token = $token;
    }
}
