<?php

namespace App\External;

// 1. Stwórz custom relation class
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Client\Factory as HttpClient;

class ApiRelation extends Relation
{
    protected $httpClient;
    protected $apiEndpoint;
    protected $foreignKey;

    public function __construct($query, $parent, $apiEndpoint, $foreignKey)
    {
        $this->httpClient = app(HttpClient::class);
        $this->apiEndpoint = $apiEndpoint;
        $this->foreignKey = $foreignKey;

        parent::__construct($query, $parent);
    }

    public function addConstraints()
    {
        // Dodaj constrainty jeśli potrzebne
    }

    public function addEagerConstraints(array $models)
    {
        // Implementacja dla eager loading
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, collect());
        }

        return $models;
    }

    public function match(array $models, $results, $relation)
    {
        // Mapowanie wyników do modeli
        return $models;
    }

    public function getResults()
    {
        $foreignKeyValue = $this->parent->{$this->foreignKey};

        $response = $this->httpClient->get($this->apiEndpoint.'/'.$foreignKeyValue);

        if ($response->successful()) {
            return collect($response->json());
        }

        return collect();
    }
}
