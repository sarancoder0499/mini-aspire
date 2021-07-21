<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Declaring Variables
     */
    protected $name = "Test";
    protected $email = "test@gmail.com";
    protected $password = "secret";
    protected $amount = 100;
    protected $term = 5;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

     /**
     * Make variables value empty
     *
     * @return void
     */
    public function resetVariables()
    {
        $this->name = "";
        $this->email = "";
        $this->password = "";
    }

     /**
     * register user
     *
     * @return void
     */
    public function register()
    {
        $data = [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
        ];

        return $this->withHeaders(['Content-Type' => 'application/json'])
        ->json('POST', 'api/register', $data);

    }

    /**
     * login user
     *
     * @return void
     */
    public function login()
    {
        $data = [
            "email" => $this->email,
            "password" => $this->password,
        ];

        return $this->withHeaders(['Content-Type' => 'application/json'])
        ->json('POST', 'api/login', $data);

    }

    /**
     * create loan
     *
     * @return void
     */
    public function createLoan($login)
    {
        $data = [
            "amount" => $this->amount,
            "term" => $this->term,
        ];

        return $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $login['token']
            ])
        ->json('POST', 'api/loan/store', $data);

    }

    /**
     * approve loan
     *
     * @return void
     */
    public function approveLoan($data,$login)
    { 
        return $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $login['token']
            ])
        ->json('POST', 'api/loan/approve', $data);

    }

     /**
     * Re pay loan
     *
     * @return void
     */
    public function payLoan($data,$login)
    { 
        return $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $login['token']
            ])
        ->json('POST', 'api/payment/repay', $data);
    }

     /**
     * Get loan
     *
     * @return void
     */
    public function getLoan($data,$login)
    { 
        return $this->withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $login['token']
            ])
        ->json('POST', 'api/loan/show', $data);
    }

}
