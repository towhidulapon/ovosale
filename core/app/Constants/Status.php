<?php

namespace App\Constants;

class Status
{

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    const TICKET_OPEN   = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY  = 2;
    const TICKET_CLOSE  = 3;

    const PRIORITY_LOW    = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM  = 3;

    const DISCOUNT_PERCENT = 1;
    const DISCOUNT_FIXED   = 2;

    const TAX_TYPE_EXCLUSIVE = 1;
    const TAX_TYPE_INCLUSIVE = 2;

    const PRODUCT_TYPE_STATIC   = 1;
    const PRODUCT_TYPE_VARIABLE = 2;

    const PURCHASE_RECEIVED = 1;
    const PURCHASE_PENDING  = 2;
    const PURCHASE_ORDERED  = 3;

    const SALE_FINAL     = 1;
    const SALE_QUOTATION = 2;

    const SUPPER_ADMIN_ID     = 1;
    const SUPER_ADMIN_ROLE_ID = 1;

    const TRANSFER_SEND  = 1;

    Const PENDING = 0;
    Const APPROVED = 1;
    Const REJECTED = 2;

    const DAILY = 1;
    const WEEKLY = 2;
    const MONTHLY = 3;
    const HALF_YEARLY = 4;
    const YEARLY = 5;



}
