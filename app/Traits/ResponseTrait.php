<?php

namespace App\Traits;

use App\Enums\HTTPCodeEnum;
use App\Enums\HTTPStatusEnum;
use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * json 相应
     *
     * @param mixed|null $data
     * @param string $message
     * @param HTTPStatusEnum $status
     * @param HTTPCodeEnum $code
     * @return JsonResponse
     */
    public function response(
        mixed $data = null,
        string $message = 'ok',
        HTTPStatusEnum $status = HTTPStatusEnum::Ok,
        HTTPCodeEnum $code = HTTPCodeEnum::Success
    ): JsonResponse
    {
        $return = [
            'code'      =>  $code->value,
            'message'   =>  $message
        ];

        if ($data !== null) {
            $return['data'] = $data;
        }

        return response()->json($return, $status->value, [], JSON_BIGINT_AS_STRING);
    }
}
