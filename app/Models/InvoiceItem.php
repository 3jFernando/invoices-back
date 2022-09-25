<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'description',
        'cant', 
        'total_unit',
        'total'
    ];

    public function Invoice()
    {
        return $this->belongsToMany(Invoice::class);
    }

}
