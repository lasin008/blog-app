<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find a model by its ID with optional eager loading.
     * This method will use findOrFail internally to ensure a model is found.
     *
     * @param int $id
     * @param array $relations
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id, array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    /**
     * Create a new model.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing model.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);
        return $model->update($data);
    }

    /**
     * Delete a model by its ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id);
        return $model->delete();
    }
}
