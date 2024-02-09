<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\Sort;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    use Sort;
}
