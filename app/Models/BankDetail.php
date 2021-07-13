<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = ['user_id','bank_name','account_no','account_holder','aba_number'];
}