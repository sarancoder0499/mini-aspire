<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test required fields are configured
     *
     * @return void
     */
    public function testValidation()
    {
        $this->email = "";
        $this->password = "";
        $login = $this->login();
        $code = Config('constants.STATUS_CODE.REQUEST_VALIDATION');
        $login->assertStatus($code)
              ->assertJson([
                "status" => false,
                "code" => $code,
                "messages" => [
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."]
                ]
                ],$code);
    }

     /**
     * Test login successful
     *
     * @return void
     */
    public function testLoginSuccessful()
    {
        $this->register();
        $login = $this->login();
        $code = Config('constants.STATUS_CODE.OK');
        $login->assertStatus($code)
              ->assertJson([
              "status" => true,
              "code" => $code,
              "message" => "Login Successful",
              "user" => $login["user"],
              "token" => $login["token"],
              ],$code);
    }

     /**
     * Test login failure
     *
     * @return void
     */
    public function testLoginFailure()
    {
        $this->register();
        $this->email = "test1@gmail.com";
        $login = $this->login();
        $code = Config('constants.STATUS_CODE.UNPROCESSABLE_ENTITY');
        $login->assertStatus($code)
              ->assertJson([
                    "status" => false,
                    "code" => $code,
                    "message" => "Invalid Credentials",
                ],$code);
    }
}

