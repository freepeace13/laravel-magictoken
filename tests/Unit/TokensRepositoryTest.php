<?php

namespace MagicToken\Tests\Unit;

use Exception;
use MagicToken\Action;
use MagicToken\Tests\TestCase;
use MagicToken\DatabaseToken;
use MagicToken\Exceptions\InvalidTokenException;
use MagicToken\Tests\Dummy\GenerateRandomString;

class TokensRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokens = $this->app['magictoken.tokens'];
    }

    protected function createNewToken($action = null, $receiver = null, $maxTries = null)
    {
        return $this->tokens->create(
            $action ?? new GenerateRandomString,
            $receiver ?? 'john@example.org',
            $maxTries ?? 3
        );
    }

    protected function findToken($token)
    {
        return DatabaseToken::where('token', $token)->first();
    }

    public function test_create_new_record_and_return_token()
    {
        $token = $this->createNewToken();

        $this->assertTrue(is_string($token));
        $this->assertNotNull($this->findToken($token));
    }

    public function test_renew_existing_record_and_return_new_token()
    {
        $oldToken = $this->createNewToken();

        $this->assertNotNull($this->findToken($oldToken));

        $newToken = $this->tokens->renew($oldToken);

        $this->assertNotNull($this->findToken($newToken));

        $this->assertNotEquals($newToken, $oldToken);
    }

    public function test_created_record_has_exact_attributes()
    {
        $record = $this->findToken($this->createNewToken());

        $this->assertNotNull($record);

        $this->assertInstanceOf(GenerateRandomString::class, $record->action);
        $this->assertEquals('john@example.org', $record->receiver);
        $this->assertEquals(3, $record->max_tries);
    }

    public function test_renewed_record_has_same_attributes()
    {
        $oldToken = $this->findToken($this->createNewToken());

        $this->assertNotNull($oldToken);

        $this->assertInstanceOf(GenerateRandomString::class, $oldToken->action);
        $this->assertEquals($oldToken->receiver, 'john@example.org');
        $this->assertEquals($oldToken->max_tries, 3);

        $this->assertNotNull(
            $newToken = $this->findToken($this->tokens->renew($oldToken->token))
        );

        $this->assertNull($this->findToken($oldToken->token));

        $this->assertInstanceOf(GenerateRandomString::class, $newToken->action);
        $this->assertInstanceOf(GenerateRandomString::class, $oldToken->action);

        $this->assertNotEquals($newToken->code, $oldToken->code);
        $this->assertNotEquals($newToken->token, $oldToken->token);

        $this->assertEquals($oldToken->receiver, $oldToken->receiver);
        $this->assertEquals($oldToken->max_tries, $oldToken->max_tries);
    }

    public function test_incorrect_pincode_will_increment_attempts()
    {
        $token = $this->createNewToken(null, null, 3);

        $this->assertNotNull($token);
        $this->assertEquals(0, $this->findToken($token)->num_tries);

        $result = $this->tokens->attempt($token, 3331);

        $this->assertIsBool($result);
        $this->assertFalse($result);
        $this->assertEquals(1, $this->findToken($token)->num_tries);

        $result = $this->tokens->attempt($token, 3331);

        $this->assertIsBool($result);
        $this->assertFalse($result);
        $this->assertEquals(2, $this->findToken($token)->num_tries);
    }

    public function test_correct_pincode_will_return_action_instance()
    {
        $token = $this->createNewToken(null, null, 3);

        $this->assertNotNull($token);
        $this->assertEquals(0, $this->findToken($token)->num_tries);

        $result = $this->tokens->attempt($token, $this->findToken($token)->code);

        $this->assertInstanceOf(Action::class, $result);
        $this->assertNull($this->findToken($token));
    }

    public function test_max_tries_reached_exception_on_incorrect_pincode()
    {
        $token = $this->createNewToken(null, null, 3);

        $this->assertEquals(0, $this->findToken($token)->num_tries);

        $firstTry = $this->tokens->attempt($token, 3331);
        $record = $this->findToken($token);

        $this->assertFalse($firstTry);
        $this->assertEquals($record->num_tries, 1);

        $secondTry = $this->tokens->attempt($token, 3331);
        $record = $this->findToken($token);

        $this->assertFalse($secondTry);
        $this->assertEquals($record->num_tries, 2);

        $thirdTry = $this->tokens->attempt($token, 3331);
        $record = $this->findToken($token);

        $this->assertFalse($thirdTry);
        $this->assertEquals($record->num_tries, 3);

        try  {
            $this->tokens->attempt($token, 3331);
        } catch (Exception $e) {
            $record = $this->findToken($token);

            $this->assertEquals($record->num_tries, $record->max_tries);
            $this->assertInstanceOf(InvalidTokenException::class, $e);
        }
    }
}
