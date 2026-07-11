<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Response formatter terpusat agar semua endpoint punya bentuk JSON yang konsisten.
 */
class ApiResponse
{
    /**
     * Response biasa (bukan paginate): hanya code, message, dan data.
     * Dipakai untuk show, store, update, dan destroy.
     *
     * @param  mixed  $data
     */
    public static function success($data = null, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Response untuk data yang di-paginate (GET list).
     * Menambahkan info halaman: page, per_page, query, dan limit.
     *
     * @param  mixed  $data  Data yang sudah diformat (mis. CourierResource collection).
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        $data,
        string $message = 'OK',
        ?string $query = null,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'query' => $query,
            'limit' => $paginator->perPage(),
            'data' => $data,
        ], $code);
    }
}
