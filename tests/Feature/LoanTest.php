<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test required fields are configured
     *
     * @return void
     */
    public function testValidation()
    {
        $code = Config('constants.STATUS_CODE.REQUEST_VALIDATION');
        $this->register();
        $login = $this->login();

        $this->amount = 0;
        $this->term = 0;

        $loan = $this->createLoan($login);
        $loan->assertStatus($code)
              ->assertJson([
                "status" => false,
                "code" => $code,
                "messages" => [
                    "amount" => ["The amount must be greater than 0."],
                    "term" => ["The term must be greater than 0."]
                ]
                ],$code);
    }

    /**
     * Test with negative values
     *
     * @return void
     */
    public function testWithNegativeValues()
    {
        $code = Config('constants.STATUS_CODE.REQUEST_VALIDATION');
        $this->register();
        $login = $this->login();
        
        $this->amount = -100;
        $this->term = 3;

        $loan = $this->createLoan($login);
        $loan->assertStatus($code)
              ->assertJson([
                "status" => false,
                "code" => $code,
                "messages" => [
                    "amount" => ["The amount must be greater than 0."]
                ]
                ],$code);
    }

    /**
     * Test with invalid value
     *
     * @return void
     */
    public function testWithInvalidValues()
    {
        $code = Config('constants.STATUS_CODE.OK');
        $this->register();
        $login = $this->login();
        
        $this->amount = 1;
        $this->term = 2;

        $loan = $this->createLoan($login);
        $loan->assertStatus($code)
              ->assertJson([
                "status" => false,
                "code" => $code,
                "message" => "Loan Invalid",
                ],$code);
    }

    /**
     * Test with correct value
     *
     * @return void
     */
    public function testWithCorrectValues()
    {
        $code = Config('constants.STATUS_CODE.OK');
        $this->register();
        $login = $this->login();
        
        $this->amount = 2;
        $this->term = 1;

        $loan = $this->createLoan($login);
        $loan->assertStatus($code)
              ->assertJson([
                "status" => true,
                "code" => $code,
                "message" => "Loan Created Successfully",
                "data" => $loan["data"],
                ],$code);
    }
    
}
