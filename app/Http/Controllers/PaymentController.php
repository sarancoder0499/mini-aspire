<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Service\LoanService;
use App\Http\Service\PaymentService;
use App\Http\Requests\Payment\PayRequest;
use Exception;
use DB;

class PaymentController extends Controller
{
    protected $service;
    protected $loanService;


    public function __construct(PaymentService $service, LoanService $loanService)
    {
        $this->service = $service;
        $this->loanService = $loanService;
    }

    /**
     * Re-Pay amount against loan
     *
     * @method show
     *
     * @param PayRequest Request
     *
     * @return \Illuminate\Http\JsonResponse
     */

     /**
        * @OA\Post(
        ** path="/api/payment/repay",
        *   tags={"Payment"},
        *   summary="- Pay amount against loan ",
        *   operationId="repay",
        *   security={
        *       {
        *           "passport": {}
        *       },
        *   },
        *   @OA\Parameter(
        *      name="loan",
        *      in="query",
        *      required=true,
        *      @OA\Schema(
        *           type="integer",
        *      )
        *   ),
        *   @OA\Response(
        *      response=200,
        *      description="Success",
        *      @OA\MediaType(
        *           mediaType="application/json",
        *     )
        *   ),
        *   @OA\Response(
        *    response=222,
        *    description="Validation Error Messages",
        *    @OA\JsonContent(
        *       @OA\Property(property="status", type="string", example="false"),
        *       @OA\Property(property="result", type="string", example="[]"),
        *    )
        *  ),
        *)
    **/
    public function pay(PayRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Fetch Record to confirm
            $loan = $this->loanService->get($request->all());

            if (!isset($loan)) {
                return $this->sendSuccessResponse("No record found");
            }

            $data = [ "data" => $loan ];

            // Check for Approval Status and revert
            if (!$loan->is_approved) {
                return $this->sendSuccessResponse("Loan not approved to repay", $data);
            }

            // Check for Loan already paid status
            if ($loan->is_paid) {
                return $this->sendSuccessResponse("Loan already paid", $data);
            }

            // Pay one EMI
            $this->service->pay($loan);

            // Update loan status
            $this->loanService->update($loan);

            DB::commit();
            return $this->sendSuccessResponse("Payment Successful", $data);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR', $e->getMessage());
        }
    }
}
