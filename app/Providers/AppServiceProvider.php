<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Support\ServiceProvider;
use Exception;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('whereHasIn', function(string $relationName, callable $callable) {
            return with(new WhereHasIn($this,$relationName,$callable))->execute();
        });
    }
}

class WhereHasIn
{
    protected $builder, $relationName, $callable;

    /**
     * WhereHasIn constructor.
     * @param \Illuminate\Database\Eloquent\Builder $builder laravel构建查询的对象,如DockerModel::query()可获得
     * @param string $relationName 关系名,与whereHas写法一样
     * @param callable $callable 闭包,与whereHas写法一样
     */
    public function __construct(Builder $builder, string $relationName, callable $callable)
    {
        $this->builder = $builder;
        $this->relationName = $relationName;
        $this->callable = $callable;
    }

    /**
     * 进行whereHasIn方式构造查询对象并返回
     * @param Builder $builder
     * @param string $relationName
     * @param callable $callable
     * @return Builder
     * @throws Exception
     */
    protected function whereHasIn(Builder $builder, string $relationName, callable $callable)
    {
        if (!$relationName) return $builder;
        $relationNames = explode('.', $relationName);
        $nextRelation = implode('.', array_slice($relationNames, 1));

        $method = $relationNames[0];
        $model = $builder->getModel();
        /** @var Relations\BelongsTo|Relations\HasOne $relation */
        $relation = Relations\Relation::noConstraints(function () use ($method, $model) {
            return $model->$method();
        });
        /** @var Builder $in,$in是关联模型的构造查询对象 */
        $in = $this->whereHasIn($relation->getQuery(), $nextRelation, $callable);
        // $in = $relation->getQuery()->whereHasIn($relation->getQuery(),$relation->getModel(),$nextRelation, $callable);
        // 调用闭包函数，并将关联模型的构造查询对象传入
        call_user_func($callable, $in);
        if ($relation instanceof Relations\BelongsTo) {
            return $builder->whereIn($relation->getForeignKeyName(), $in->select($relation->getOwnerKeyName()));
        } elseif ($relation instanceof Relations\HasOne) {
            return $builder->whereIn($model->getKeyName(), $in->select($relation->getForeignKeyName()));
        }

        throw new Exception(__METHOD__ . " 不支持 " . get_class($relation));
    }

    public function execute()
    {
        try {
            return $this->whereHasIn($this->builder, $this->relationName, $this->callable);
        } catch (Exception $e) {
            throw $e;
        }
    }
}