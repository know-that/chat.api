<?php

namespace App\Traits\Model;

use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use App\Rewrite\LengthAwarePaginator;
use Laravel\Scout\Builder as ScoutBuilder;
use Laravel\Scout\Contracts\PaginatesEloquentModels;

trait BootTrait
{
    /**
     * 设置主键非自增而是雪花编号
     * @return void
     */
    public static function snowflakeId(): void
    {
        self::creating(static function ($model) {
            $snowflake = new Snowflake();
            $snowflake->setStartTimeStamp(strtotime('2023-01-01') * 1000)
                ->setSequenceResolver(new LaravelSequenceResolver(app('cache')->store()));
            $model->{$model->getKeyName()} = $snowflake->id();
        });
    }

    /**
     * 添加 builder normalPaginate 方法
     * @return void
     */
    public static function normalPaginatorMacro(): void
    {
        Builder::macro('normalPaginate', function ($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $perPage = $perPage ?: $this->model->getPerPage();

            $total = $this->toBase()->getCountForPagination();

            $results = $total
                ? $this->forPage($page, $perPage)->get($columns)
                : $this->model->newCollection();

            $options = [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ];

            return new LengthAwarePaginator($results, $total, $perPage, $page, $options);
        });
    }

    /**
     * 添加 Scout Builder normalPaginate 方法
     * @return void
     */
    public static function scoutNormalPaginatorMacro(): void
    {
        ScoutBuilder::macro('normalPaginate', function ($perPage = null, $columns = ['*'], $pageName = 'page', $page = null) {
            $engine = $this->engine();

            if ($engine instanceof PaginatesEloquentModels) {
                return $engine->paginate($this, $perPage, $page)->appends('query', $this->query);
            }

            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $perPage = $perPage ?: $this->model->getPerPage();

            $results = $this->model->newCollection($engine->map(
                $this, $rawResults = $engine->paginate($this, $perPage, $page), $this->model
            )->all());

            return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
                'items' => $results,
                'total' => $this->getTotalCount($rawResults),
                'perPage' => $perPage,
                'currentPage' => $page,
                'options' => [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ],
            ])->appends('query', $this->query);
        });
    }

    /**
     * 添加 builder orSearch 方法
     * @return void
     */
    public static function orSearchMacro(): void
    {
        Builder::macro('orSearch', function (string $search) {
            return $this->where('id', $search)
                ->orWhere('email', $search)
                ->orWhere('name', 'like', "{$search}%")
                ->orWhere('nickname', 'like', "{$search}%");
        });
    }
}
