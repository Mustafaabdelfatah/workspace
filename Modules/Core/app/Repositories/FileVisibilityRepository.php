<?php
namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Models\FileVisibility;

class FileVisibilityRepository
{
    /**
     * Get FileVisibility list without pagination
     *
     * @param mixed $args
     *
     * @return mixed
     */
    public function getFileVisibilityWithoutPaginate(mixed $args): mixed
    {
        $fileVisibilities = FileVisibility::likeTitle($args)
            ->orderBy('id', 'asc')
            ->get();

        return [
            'status' => true,
            'message' => __('core::messages.file_visibility_fetch_success'),
            'data' => $fileVisibilities,
        ];
    }

    /**
     * Generate paging array
     *
     * @param array $args
     *
     * @return array
     */
    public function pagingArray(mixed $collecion): array
    {
        return [
            'total' => $collecion->total(),
            'per_page' => $collecion->perPage(),
            'current_page' => $collecion->currentPage(),
            'last_page' => $collecion->lastPage(),
            'from' => $collecion->firstItem(),
            'to' => $collecion->lastItem()
        ];
    }
}
