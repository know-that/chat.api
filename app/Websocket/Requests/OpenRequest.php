<?php

namespace App\Websocket\Requests;

use JetBrains\PhpStorm\ArrayShape;

class OpenRequest extends BaseRequest
{
    /**
     * 验证字段
     * @return string[]
     */
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            'state'     => 'require|boolean',
            'name'      => 'require|string|between:1,50',
            'page'      => 'nullable|integer|digits_between:1,18',
            'limit'     => 'nullable|integer|digits_between:1,18'
        ];
    }

    /**
     * 字段别名
     * @return string[]
     */
    #[ArrayShape([])]
    public function attributes(): array
    {
        return [
            'state'     => '状态',
            'search'    => '搜索条件',
            'page'      => '当前页',
            'limit'     => '页码'
        ];
    }
}
