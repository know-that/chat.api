<?php

namespace App\Services\AliYun;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Result\Result;
use App\Exceptions\ErrorException;
use App\Traits\InstanceTrait;
use Exception;

class AliYunService extends AliYun
{
    use InstanceTrait;

    /**
     * constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        try {
            AlibabaCloud::accessKeyClient($this->config['accessKeyId'], $this->config['accessKeySecret'])
                ->regionId($this->config['OSS']['region'])
                ->asDefaultClient();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取 aliyun 临时 AccessKey
     *
     * @param string $moduleName
     * @return Result
     * @throws Exception
     */
    public function getAssumeRole(string $moduleName = 'know-that-admin'): Result
    {
        try {
            $result = AlibabaCloud::rpc()
            ->product('Sts')
            ->scheme('https')
            ->version('2015-04-01')
            ->action('AssumeRole')
            ->method('POST')
            ->host('sts.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => $this->config['OSS']['region'],
                    'RoleArn' => $this->config['roleArn'],
                    'RoleSessionName' => $moduleName,
                ],
            ])
            ->request();
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }
}
