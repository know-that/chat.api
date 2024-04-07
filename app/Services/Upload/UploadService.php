<?php

namespace App\Services\Upload;

use App\Models\Upload;
use App\Traits\InstanceTrait;
use Exception;

class UploadService implements AsyncUploadInterface
{
    use InstanceTrait;

    /**
     * 驱动
     * @var AsyncUploadInterface
     */
    private AsyncUploadInterface $driver;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->driver = new QiNiuKoDo();
    }

    /**
     * 授权信息
     * @return array
     * @throws Exception
     */
    public function credentials(): array
    {
        return [
            'driver'    => class_basename($this->driver::class),
            'data'      => $this->driver->credentials()
        ];
    }

    /**
     * 回调
     * @param array $params
     * @return Upload
     */
    public function callback(array $params): Upload
    {
        return $this->driver->callback($params);
    }
}
