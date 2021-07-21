<?php

namespace App\Http\Controllers;

use App\Http\Requests\Loan\StoreRequest;
use App\Http\Requests\Loan\ShowRequest;
use App\Http\Requests\Loan\ApproveRequest;
use App\Http\Service\LoanService;
use App\Http\Service\PaymentService;
use Exception;
use DB;

class LoanController extends Controller
{
    protected $service;
    protected $paymentService;


    public function __construct(LoanService $service, PaymentService $paymentService)
    {
        $this->service = $service;
        $this->paymentService = $paymentService;
    }

    /**
     * Fetch All Loans
     *
     * @method index
     * 
     * 
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     */
     /**
        * @OA\Post(
        ** path="/api/loan/all",
        *   tags={"Loan"},
        *   summary="- Fetch all pending and approved loans.",
        *   operationId="all",
        *   security={
        *       {
        *           "passport": {}
        *       },
        *   },
        *   @OA\Response(
        *      response=200,
        *      description="Success",
        *      @OA\MediaType(
        *           mediaType="application/json",
        *      )
        *   ),
        *)
    **/
    public function index()
    {
        try{
            
            $data = $this->service->all();
            $msg = (count($data) > 0) ? "Record's Found" : "No records found";
            
            $data = [ "data" => $data ];
            return $this->sendSuccessResponse($msg,$data);

        }catch(Exception $e)
        {
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR',$e->getMessage());
        }
    }

    /**
     * Register new Loan
     *
     * @method store
     * 
     * @param StoreRequest Request
     * 
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     */

     /**
        * @OA\Post(
        ** path="/api/loan/store",
        *   tags={"Loan"},
        *   summary="- Store loan and repay records",
        *   operationId="store",
        *   security={
        *       {
        *           "passport": {}
        *       },
        *   },
        *   @OA\Parameter(
        *      name="amount",
        *      in="query",
        *      required=true,
        *      @OA\Schema(
        *           type="integer",
        *      )
        *   ),
        *   @OA\Parameter(
        *      name="term",
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
        *      )
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
    public function store(StoreRequest $request)
    {
        try{
            DB::beginTransaction();
            
            // check basic criteria
            $amount = $request["amount"];
            $term = $request["term"];
            
            $emi = round($amount / $term);
            $total = $term * $emi;
            
            if($amount < $total)
            {
                return $this->sendErrorResponse("OK","Loan Invalid");
            }

            // create new loan record
            $loan = $this->service->store($request->all());

            // Process terms records
            $payment = $this->paymentService->store($loan);

            // update Loan EMI
            $loan->emi = $payment;
            $loan->save();

            DB::commit();

            $data = [ "data" => $loan ];
            return $this->sendSuccessResponse("Loan Created Successfully",$data);

        }catch(Exception $e)
        {
            DB::rollback();
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR',$e->getMessage());
        }
    }


    /**
     * Fetch Single Loan based on Loan Id
     *
     * @method show
     * 
     * @param ShowRequest Request
     * 
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     */

     /**
        * @OA\Post(
        ** path="/api/loan/show",
        *   tags={"Loan"},
        *   summary="- Show single loan based on ID",
        *   operationId="show",
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
        *      )
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
    public function show(ShowRequest $request)
    {
        try{

            $data = $this->service->get($request->all());
            $msg = (isset($data)) ? "Record's Found" : "No records found";

            $data = [ "data" => $data ];
            return $this->sendSuccessResponse($msg,$data);

        }catch(Exception $e)
        {
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR',$e->getMessage());
        }
    }

    /**
     * Approve Pending Loan
     *
     * @method show
     * 
     * @param ShowRequest Request
     * 
     * @return Illuminate\Database\Eloquent\Collection  [\App\Models\Loan]
     */

     /**
        * @OA\Post(
        ** path="/api/loan/approve",
        *   tags={"Loan"},
        *   summary="- Approve pending loan",
        *   operationId="approve",
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
        *      )
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
    public function approve(ApproveRequest $request)
    {
        try{
            // Fetch Record to confirm
            $loan = $this->service->get($request->all());
            
            if(!isset($loan))
            {
                return $this->sendSuccessResponse("No record found");
            }
            // Check for Approval Status and revert
            if($loan->is_approved)
            {
                $data = [ "data" => $loan ];
                return $this->sendSuccessResponse("Loan approved already",$data);   
            }

            // Approve Loan
            $this->service->approve($loan); 

            $data = [ "data" => $loan ];
            return $this->sendSuccessResponse("Loan approved",$data);

        }catch(Exception $e)
        {
            return $this->sendErrorResponse('INTERNAL_SERVER_ERROR',$e->getMessage());
        }
    }
}
