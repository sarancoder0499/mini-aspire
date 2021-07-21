<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanApproveTest extends TestCase
{
    use RefreshDatabase;

     /**
     * test with no loan record
     *
     * @return void
     */
    public function testWithNoLoanRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');
        $data = [
            "loan" => 200,
        ];

        $approve = $this->approveLoan($data,$login);        
        $approve->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "No record found",
                ],$code);
    }

     /**
     * test with loan record
     *
     * @return void
     */
    public function testWithNotApprovedRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');

        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];

        $approve = $this->approveLoan($data,$login);
        $approve->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "Loan approved",
                    "data" => $approve["data"],
                ],$code);
    }

    /**
     * test with already approved loan record
     *
     * @return void
     */
    public function testWithApprovedRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');

        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];

        $this->approveLoan($data,$login);

        $approve = $this->approveLoan($data,$login);
        $approve->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "Loan approved already",
                    "data" => $approve["data"],
                ],$code);
    }
}
