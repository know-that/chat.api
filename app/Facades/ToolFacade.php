<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\ToolService;

/**
 * @method static array tree(mixed $data, int $pid = 0)
 * @method static array childrenAll(array $data, int $id, array $new = [])
 * @method static array parentAll(array $data, int $id, array $new = [])
 * @method static array treeFilter(array $arr, array $values = ['', null, false, 0, '0',[]])
 * @method static string makeLangCode(string $type = '')
 * @method static string strHidden(string $str, int $length = 4, string $padString = '*')
 * @method static bool isLink(string $str)
 * @method static void iterableForget(iterable &$iterable, string $key)
 *
 * @see ToolService
 * @package App\Facades\AliYun
 */
class ToolFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ToolService::class;
    }
}
