<?php namespace App\Repositories;

use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceRepository {

  public function getInvoices() 
  {
      return Invoice::with(['Items'])->get();
  }

  public function getInvoiceById(int $id) 
  {
      return Invoice::with('items')->find($id);
  }

  public function storeInvoice(object $request)
  {
    $invoice = new Invoice();   
    
    $invoice->number = $request->number;
    $invoice->transmitter_name = $request->transmitter_name;
    $invoice->transmitter_nit = $request->transmitter_nit;
    $invoice->receiver_name = $request->receiver_name;
    $invoice->receiver_nit = $request->receiver_nit;
    $invoice->subtotal = $request->subtotal;
    $invoice->tax = $request->tax;
    $invoice->total = $request->total;
    
    $invoice->save();

    return $invoice;
  }

  public function storeInvoiceItem(object $item)
  {

    $invoiceItem = new InvoiceItem();

    $invoiceItem->invoice_id = $item->invoiceId;
    $invoiceItem->description = $item->description;
    $invoiceItem->cant = $item->cant;
    $invoiceItem->total_unit = $item->total_unit;
    $invoiceItem->total = $item->total;
    
    $invoiceItem->save();

    return $invoiceItem;
  }

}

   