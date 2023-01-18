<?php

namespace App\Websocket\Requests;

use App\Exceptions\ParameterException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BaseRequest extends FormRequest
{
    /**
     * 单个验证失败后停止
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 验证完毕后
     *
     * @param Validator $validator
     * @return void
     * @throws ParameterException
     */
    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            throw new ParameterException($validator->errors()->first());
        }
    }
}
