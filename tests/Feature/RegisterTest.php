<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test required fields are configured
     *
     * @return void
     */
    public function testValidation()
    {
        $this->resetVariables();
        $register = $this->register();
        $code = Config('constants.STATUS_CODE.REQUEST_VALIDATION');
        $register->assertStatus($code)
              ->assertJson([
                "status" => false,
                "code" => $code,
                "messages" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."]
                ]
                ],$code);
    }

    /**
     * Test register successful
     *
     * @return void
     */
    public function testRegisterSuccessful()
    {
        $code = Config('constants.STATUS_CODE.OK');
        $register = $this->register();
        $register->assertStatus($code)
              ->assertJson([
              "status" => true,
              "code" => $code,
              "message" => "Registration Success",
              "user" => $register["user"],
              "token" => $register["token"],
              ],$code);
    }

    /**
     * Test register failure
     *
     * @return void
     */
    public function testRegisterFailure()
    {
        $code = Config('constants.STATUS_CODE.REQUEST_VALIDATION');
        $this->register();
        $register = $this->register();
        $register->assertStatus($code)
                 ->assertJson([
                        "status" => false,
                        "code" => $code,
                        "messages" => [
                            "email" => ["The email has already been taken."],
                        ]
                    ],$code);
    }

}
