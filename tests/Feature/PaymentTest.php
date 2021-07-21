<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
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

        $pay = $this->payLoan($data,$login);        
        $pay->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "No record found",
                ],$code);
    }

    /**
     * test with not approved record
     *
     * @return void
     */
    public function testWithNotApprovedLoanRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];

        $pay = $this->payLoan($data,$login);        
        $pay->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "Loan not approved to repay",
                ],$code);
    }

    /**
     * test with approved record
     *
     * @return void
     */
    public function testWithApprovedLoanRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];
        
        $this->approveLoan($data,$login);

        $pay = $this->payLoan($data,$login);
        
        $pay->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "Payment Successful",
                ],$code);
    }

    /**
     * test with paid loan record
     *
     * @return void
     */
    public function testWithPaidLoanRecord()
    {
        $this->register();
        $login = $this->login();

        $code = Config('constants.STATUS_CODE.OK');
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];
        
        $this->approveLoan($data,$login);

        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $pay = $this->payLoan($data,$login);
        
        $pay->assertStatus($code)
                ->assertJson([
                    "status" => true,
                    "code" => $code,
                    "message" => "Loan already paid",
                ],$code);
    }

    /**
     * test with paid loan record
     *
     * @return void
     */
    public function testWithLoanBalanceAfterPaidLoanRecord()
    {
        $this->register();
        $login = $this->login();
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];
        
        $this->approveLoan($data,$login);

        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        
        $loan = $this->getLoan($data,$login);
        
        $this->assertEquals($loan["data"]["loan_balance"],0);
    }

    /**
     * test with term loan record
     *
     * @return void
     */
    public function testWithTermBalanceAfterPaidLoanRecord()
    {
        $this->register();
        $login = $this->login();
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];
        
        $this->approveLoan($data,$login);

        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        $this->payLoan($data,$login);
        
        $loan = $this->getLoan($data,$login);
        
        $this->assertEquals($loan["data"]["term_balance"],0);
    }

    /**
     * test with emi count record
     *
     * @return void
     */
    public function testWithEmiCountRecord()
    {
        $this->register();
        $login = $this->login();
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];

        $loan = $this->getLoan($data,$login);
        $terms = count($loan["data"]["payments"]);
        
        $this->assertEquals($terms,$this->term);
    }

    /**
     * test with loan and emi payment
     *
     * @return void
     */
    public function testWithLoanAndEmiCheck()
    {
        $this->register();
        $login = $this->login();
        
        $this->amount = 111;
        $this->term = 100;

        $loan = $this->createLoan($login);
    
        $data = [
            "loan" => $loan["data"]["id"],
        ];

        $loan = $this->getLoan($data,$login);
        
        $terms = $loan["data"]["payments"];
        
        $termAmount = 0;
        foreach($terms as $term)
        {
            $termAmount += $term["payment"];
        }
        $this->assertEquals($loan["data"]["amount"],$termAmount);
    }

    /**
     * test with term loan record
     *
     * @return void
     */
    public function testWithTermDecrement()
    {
        $this->register();
        $login = $this->login();
        
        $loan = $this->createLoan($login);
        $data = [
            "loan" => $loan["data"]["id"],
        ];
        
        $this->approveLoan($data,$login);

        $this->payLoan($data,$login);
        
        $loan = $this->getLoan($data,$login);
        
        $this->assertEquals($loan["data"]["term_balance"],$loan["data"]["term"] - 1);
    }
}
