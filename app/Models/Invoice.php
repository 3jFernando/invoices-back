<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'number',
        'transmitter_name',
        'transmitter_nit',
        'receiver_name',
        'receiver_nit',
        'subtotal',
        'tax',
        'total'
    ];

    public function Items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

}
