<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $guarded  = ['id'];
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function stockTransferDetails()
    {
        return $this->hasMany(StockTransferDetail::class);
    }
}
