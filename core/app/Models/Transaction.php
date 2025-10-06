<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $guarded  = ['id'];

    public function exportColumns(): array
    {
        return  [
            'payment_account_id' => [
                'name' => "payment account",
                'callback' => function ($item) {
                    return @$item->paymentAccount->account_name;
                }
            ],
            'trx',
            'created_at' => [
                'name' =>  "transacted",
                'callback' => function ($item) {
                    return showDateTime($item->created_at, lang: 'en');
                }
            ],
            'amount' => [
                'callback' => function ($item) {
                    return showAmount($item->amount);
                }
            ],
            'post_balance' => [
                'callback' => function ($item) {
                    return showAmount($item->post_balance);
                }
            ],
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id');
    }
}
