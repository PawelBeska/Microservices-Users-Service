<?php

namespace App\External\Models\Relations;

use App\Enums\ServiceEnum;
use App\Models\External;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExternalRelation extends Relation
{
    public function __construct(
        Builder $query,
        Model $parent,
        private ServiceEnum $serviceEnum,
        private string $localKey,
        private string $table
    ) {
        parent::__construct($query, $parent);
    }

    public function addConstraints(): void
    {
    }

    public function addEagerConstraints(array $models): void
    {
        $keys = collect($models)->pluck($this->localKey)->unique()->values();

        $externals = External::query()
            ->join('services', 'externals.service_id', '=', 'services.id')
            ->where('services.service', $this->serviceEnum)
            ->whereIn('externals.id', $keys)
            ->select('externals.*', 'services.service', 'services.host', 'services.port') // Dodaj więcej pól, jeśli potrzebujesz
            ->get();

        if ($externals->isNotEmpty()) {
            $this->prefetchExternalData($externals);
        }
    }

    protected function prefetchExternalData(Collection $externals): void
    {
        $externals->groupBy('service_id')->each(function (Collection $data) {
            $this->batchFetchFromService($data);
        });
    }

    protected function batchFetchFromService(Collection $data): void
    {
        $service = $data->first();

        $url = "http://{$service->host}:{$service->port}/api/v1/external-relations/{$this->table}/batch";


        $notCachedData = $data->mapWithKeys(function (External $item) use ($service) {
            return [$item->external_id => sprintf('external_prefetch:%s', $item->id)];
        })->filter(fn(string|int $value, string|int $key): bool => Cache::missing($value))->toArray();

        $response = Http::timeout(30)->post($url, [
            'ids' => array_keys($notCachedData),
        ]);

        if ($response->successful()) {
            $results = $response->collect('data');

            Cache::putMany(
                $results->mapWithKeys(function ($result) use ($data) {
                    if ($key = $data->where('external_id', $result['id'])->first()?->id) {
                        return [
                            sprintf(
                                'external_prefetch:%s',
                                $key,
                            ) => $result
                        ];
                    }

                    return [];
                })->filter()->toArray(),
                300
            );
        }
    }


    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, collect());
        }

        return $models;
    }

    public function match(array $models, $results, $relation): array
    {
        $cacheKeys = collect($models)->map(function (Model $model) {
            return sprintf('external_prefetch:%s', $model->{$this->localKey});
        });

        $cachedData = collect(Cache::getMultiple($cacheKeys->toArray()))
            ->mapWithKeys(fn(?array $val, string $key): array => [Str::afterLast($key, ':') => $val]);

        foreach ($models as $model) {
            $key = $model->{$this->localKey};

            $model->setRelation($relation, collect($cachedData->get($key) ?? []));
        }

        return $models;
    }

    // Override dla query builder methods

    public function getResults()
    {
        return collect();
    }

    public function getEager()
    {
        return collect();
    }
}
