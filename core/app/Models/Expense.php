<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    
    protected $guarded  = ['id'];
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, "added_by");
    }
    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id');
    }
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function exportColumns(): array
    {
        return  [
            'category_id' => [
                'name' => "purpose",
                'callback' => function ($item) {
                    return @$item->category->name;
                }
            ],
            'expense_date',
            'reference_no',
            'comment',
            "amount" => [
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ]
        ];
    }
}
