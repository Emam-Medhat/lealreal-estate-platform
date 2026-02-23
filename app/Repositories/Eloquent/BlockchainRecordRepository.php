<?php

namespace App\Repositories\Eloquent;

use App\Models\BlockchainRecord;
use App\Repositories\Contracts\BlockchainRecordRepositoryInterface;
use Illuminate\Support\Collection;

class BlockchainRecordRepository extends BaseRepository implements BlockchainRecordRepositoryInterface
{
    /**
     * BlockchainRecordRepository constructor.
     *
     * @param BlockchainRecord $model
     */
    public function __construct(BlockchainRecord $model)
    {
        parent::__construct($model);
    }

    public function getRecent(int $limit = 5): Collection
    {
        return $this->remember("blockchain_records_recent_{$limit}", function () use ($limit) {
            return $this->model->orderBy('created_at', 'desc')->limit($limit)->get();
        }, [], 60);
    }

    public function getStats()
    {
        return $this->remember("blockchain_stats", function () {
            return [
                'total_blocks' => $this->model->count(),
                'total_transactions' => $this->model->sum('transaction_count'),
                'total_difficulty' => $this->model->sum('difficulty'),
                'average_block_size' => $this->model->avg('size'),
                'average_difficulty' => $this->model->avg('difficulty'),
                'confirmed_blocks' => $this->model->where('status', 'confirmed')->count(),
                'pending_blocks' => $this->model->where('status', 'pending')->count(),
                'orphaned_blocks' => $this->model->where('status', 'orphaned')->count(),
                'total_miners' => $this->model->distinct('miner')->count('miner'),
                'latest_height' => $this->model->max('height'),
                'difficulty' => $this->model->latest()->value('difficulty') ?? 0,
            ];
        }, [], 300);
    }

    public function getLatestBlock()
    {
        return $this->remember("blockchain_latest_block", function () {
            return $this->model->latest()->first();
        }, [], 60);
    }

    public function findByHash(string $hash)
    {
        return $this->remember("blockchain_block_{$hash}", function () use ($hash) {
            return $this->model->where('hash', $hash)->first();
        }, [], 3600);
    }

    public function findByHeight(int $height)
    {
        return $this->remember("blockchain_block_height_{$height}", function () use ($height) {
            return $this->model->where('height', $height)->first();
        }, [], 3600);
    }

    public function getPaginatedRecords(int $limit = 50, int $offset = 0)
    {
        return $this->remember("blockchain_records_paginated_{$limit}_{$offset}", function () use ($limit, $offset) {
            return $this->model->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }, [], 60);
    }
}
