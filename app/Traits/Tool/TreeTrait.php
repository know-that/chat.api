<?php

namespace App\Traits\Tool;

trait TreeTrait
{
    /**
     * 递归成分类树
     *
     * @param iterable $data 数据
     * @param int $level 最多生成多少级
     * @param int $pid 父级编号
     * @param array $levelDownIds 达到最大等级后的下级所有编号
     * @return iterable
     */
    public function tree(mixed $data, int $level = 0, int $pid = 0, array $levelDownIds = []): iterable
    {
        $new = [];
        foreach ($data as $k=>$v) {
            // 判断 level 跟上级编号
            if (isset($v['level']) && (int) $v['level'] >= $level && in_array($v['pid'], $levelDownIds)) {
                $levelDownIds[] = $v['id']; // 记录上级编号
                $new[] = $v;
            } else if((int) $v['pid'] === $pid ) {
                unset($data[$k]); // 删除 data 单元，防止重复遍历
                $v['children'] = $this->tree($data, $level, $v['id'], [$v['id']]);

                // 如果为空则删除 children
                if (empty($v['children'])) {
                    unset($v['children']);
                }
                $new[] = $v;
            }
        }

        return $new;
    }

    /**
     * 所有子级
     *
     * @param array $data
     * @param int $id
     * @param array $array
     * @return array
     */
    public function childrenAll(array $data, int $id, array $array = []): array
    {
        foreach ($data as $v) {
            if($v['pid'] === $id){
                $array[] = $v;
                $array = $this->childrenAll($data, $v['id'], $array);
            }
        }

        return $array;
    }

    /**
     * 所有父级
     *
     * @param array $data
     * @param int $id
     * @param array $array
     * @return array
     */
    public function parentAll(array $data, int $id, array $array = []): array
    {
        foreach ($data as $v) {
            if($v['id'] === $id){
                $array[] = $v;
                $array = $this->parentAll($data, $v['pid'], $array);
            }
        }

        return $array;
    }

    /**
     * 分类树去空
     *
     * @param array $arr
     * @param array $values
     * @return array
     */
    public function treeFilter(array $arr, array $values = ['', null, false, 0, '0',[]]): array
    {
        foreach ($arr as $k => $v) {
            if (is_array($v) && count($v)>0) {
                $arr[$k] = $this->treeFilter($v, $values);
            }
            foreach ($values as $value) {
                if ($v === $value) {
                    unset($arr[$k]);
                    break;
                }
            }
        }
        return $arr;
    }
}
