<?php

namespace App\Services\AliYun\Trait;

use App\Enums\Model\FileUploadFromEnum;
use App\Exceptions\ErrorException;
use App\Models\Upload;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait OSSAboutModelTrait
{
    /**
     * 上传并保存文件
     * TODO 该方法相当于两次上传，一次上传到本地，一次上传到 oss
     *
     * @param string $object
     * @param string $content
     * @return mixed
     * @throws ErrorException
     */
    public function uploadStringAndCreate(string $object, string $content): mixed
    {
        // 保存图片到本地 TODO 无法不保存文件的情况下获取元信息
        Storage::disk('uploads')->put($object, $content);
        $filepath = Storage::disk('uploads')->path($object);
        $file = new File($filepath);

        DB::beginTransaction();
        try {
            // 保存数据
            $data = [
                'from'          => FileUploadFromEnum::AliYunOss->value,
                'marker'        => md5_file($filepath),
                'name'          => $file->getFilename(),
                'mime'          => $file->getMimeType(),
                'suffix'        => $file->getExtension(),
                'url'           => $object,
                'size'          => $file->getSize(),
                'created_at'    => date("Y-m-d H:i:s")
            ];
            $fileModel = Upload::firstOrcreate(['marker' => $data['marker']], $data);
            if (!$fileModel->wasRecentlyCreated) {
                return $fileModel;
            }

            // 上传到 oss
            $this->uploadString($object, $content);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // 删除文件
            Storage::disk('uploads')->delete($object);
            throw $e;
        }
        // 删除文件
        Storage::disk('uploads')->delete($object);

        return $fileModel;
    }
}
