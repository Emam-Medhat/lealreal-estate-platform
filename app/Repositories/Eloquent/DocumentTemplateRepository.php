<?php

namespace App\Repositories\Eloquent;

use App\Models\DocumentTemplate;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class DocumentTemplateRepository implements DocumentTemplateRepositoryInterface
{
    protected $model;

    public function __construct(DocumentTemplate $model)
    {
        $this->model = $model;
    }

    /**
     * Get all templates paginated
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->with(['createdBy'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get active templates
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return Cache::remember('document_templates.active', 3600, function () {
            return $this->model->active()->orderBy('name')->get();
        });
    }

    /**
     * Get templates by category
     *
     * @param string $category
     * @return Collection
     */
    public function getByCategory(string $category): Collection
    {
        return Cache::remember("document_templates.category.{$category}", 3600, function () use ($category) {
            return $this->model->byCategory($category)->active()->orderBy('name')->get();
        });
    }

    /**
     * Find template by ID
     *
     * @param int $id
     * @return DocumentTemplate|null
     */
    public function find(int $id): ?DocumentTemplate
    {
        return $this->model->find($id);
    }

    /**
     * Create new template
     *
     * @param array $data
     * @return DocumentTemplate
     */
    public function create(array $data): DocumentTemplate
    {
        $this->clearCache();
        return $this->model->create($data);
    }

    /**
     * Update template
     *
     * @param DocumentTemplate $template
     * @param array $data
     * @return bool
     */
    public function update(DocumentTemplate $template, array $data): bool
    {
        $this->clearCache();
        return $template->update($data);
    }

    /**
     * Delete template
     *
     * @param DocumentTemplate $template
     * @return bool
     */
    public function delete(DocumentTemplate $template): bool
    {
        $this->clearCache();
        return $template->delete();
    }

    /**
     * Duplicate template
     *
     * @param DocumentTemplate $template
     * @return DocumentTemplate
     */
    public function duplicate(DocumentTemplate $template): DocumentTemplate
    {
        $this->clearCache();
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (نسخة)';
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();
        return $newTemplate;
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        Cache::forget('document_templates.active');
        // We can't easily clear all category caches without a tag or pattern match, 
        // but for now this is a start. 
        // Ideally we would use tags if the cache driver supports it.
        // Cache::tags(['document_templates'])->flush();
    }
}
