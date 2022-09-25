<?php

namespace App\Http\Controllers\V1;

use App\Adapters\RequestValidAdapter;
use App\Adapters\ResponseAdapter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use JWTAuth;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
    protected $user;
    private $invoiceRepository;
    private $requestValidAdapter;
    private $responseAdapter;

    public function __construct(
        Request $request,
        InvoiceRepository $invoiceRepository,
        RequestValidAdapter $requestValidAdapter,
        ResponseAdapter $responseAdapter
    ) {

        $token = $request->header('Authorization');        

        if ($token) {
            try {
                $this->user = JWTAuth::parseToken()->authenticate();            
            } catch(\Exception $e) {
                
            }
        }


        $this->invoiceRepository = $invoiceRepository;
        $this->requestValidAdapter = $requestValidAdapter;
        $this->responseAdapter = $responseAdapter;
    }

    public function getAll()
    {
        $invoices = $this->invoiceRepository->getInvoices();

        return $this->responseAdapter->sendResponse(
            "success",
            "Listado de Facturas cargado con exito.",
            $invoices,
            200
        );
    }

    public function getById(int $id)
    {
        $status = "success";
        $message = "Factura cargada con exito.";

        $invoice = $this->invoiceRepository->getInvoiceById($id);
        if(!$invoice) {
            $status = "item_not_found";
            $message = "Factura no encontrada.";
        }

        return $this->responseAdapter->sendResponse(
            $status,
            $message,
            $invoice,
            200
        );
    }

    public function store(Request $request): Object
    {
        $validator = $this->isDataInvoiceValid($request->all());

        if (!$validator->status) {
            return $this->responseAdapter->sendResponse(
                "error_validation",
                "Error en la validación de algunos campos de la factura.",
                $validator->messages,
                200
            );
        }

        try {

            DB::beginTransaction();

            $invoice = $this->invoiceRepository->storeInvoice((object)$request->all());

            $resultStoredInvoiceItems = $this->storeInvoiceItems($invoice->id, $request->get('items'));
            if (!$resultStoredInvoiceItems->status) {
                return $this->responseAdapter->sendResponse(
                    "error_validation",
                    "Error en la validación de algunos items de la factura.",
                    $resultStoredInvoiceItems->response,
                    200
                );
            }

            DB::commit();

            $invoice->items = $resultStoredInvoiceItems->response;

            return $this->responseAdapter->sendResponse(
                "success",
                "Factura creada con exito.",
                $invoice,
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseAdapter->sendResponse(
                "error_server",
                "Error al tratar de realiar la acción.",
                $e->getMessage(),
                500
            );
        }
    }

    public function storeInvoiceItems(int $invoiceId, array $items): object
    {
        (array) $storedItems = [];
        foreach ($items as $item) {

            $validator = $this->isDataItemsInvoiceValid($item);
            if (!$validator->status) {
                return (object)[
                    'status' => false,
                    'response' => $validator->messages
                ];
            }

            $item['invoiceId'] = $invoiceId;
            $invoiceItem = $this->invoiceRepository->storeInvoiceItem((object)$item);

            $storedItems[] = $invoiceItem;
        }

        return (object)[
            'status' => true,
            'response' => $storedItems
        ];
    }

    public function isDataInvoiceValid(array $request): object
    {
        return $this->requestValidAdapter->isRequestValid($request, [
            'number' => 'required|string',
            'transmitter_name' => 'required|string',
            'transmitter_nit' => 'required|string',
            'receiver_name' => 'required|string',
            'receiver_nit' => 'required|string',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric'
        ]);
    }

    public function isDataItemsInvoiceValid(array $request): object
    {
        return $this->requestValidAdapter->isRequestValid($request, [
            'description' => 'required|string',
            'cant' => 'required|numeric',
            'total_unit' => 'required|numeric'
        ]);
    }
}
