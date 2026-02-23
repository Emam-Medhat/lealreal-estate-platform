<?php

namespace App\Repositories\Contracts;

use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DocumentTemplateRepositoryInterface
{
    /**
     * Get all templates paginated
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 20): LengthAwarePaginator;

    /**
     * Get active templates
     *
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * Get templates by category
     *
     * @param string $category
     * @return Collection
     */
    public function getByCategory(string $category): Collection;

    /**
     * Find template by ID
     *
     * @param int $id
     * @return DocumentTemplate|null
     */
    public function find(int $id): ?DocumentTemplate;

    /**
     * Create new template
     *
     * @param array $data
     * @return DocumentTemplate
     */
    public function create(array $data): DocumentTemplate;

    /**
     * Update template
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @return bool
     */
    public function update(DocumentTemplate $template, array $data): bool;

    /**
     * Delete template
     *
     * @param DocumentTemplate $template
     * @return bool
     */
    public function delete(DocumentTemplate $template): bool;

    /**
     * Duplicate template
     *
     * @param DocumentTemplate $template
     * @return DocumentTemplate
     */
    public function duplicate(DocumentTemplate $template): DocumentTemplate;
}
