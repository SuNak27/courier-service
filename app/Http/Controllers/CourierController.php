<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CourierController extends Controller
{
    /**
     * The sort options the frontend may request, mapped to real columns.
     *
     * @var array<string, string>
     */
    private const SORTABLE = [
        'name' => 'name',
        'registered_at' => 'created_at',
    ];

    /**
     * Display a paginated listing of couriers.
     *
     * Supported query parameters:
     *  - search:    match couriers whose name contains every given word
     *               (e.g. "budi agung" matches "Budiono Hadi Agung").
     *  - level:     comma-separated levels to include (e.g. "2,3").
     *  - sort:      "name" (default) or "registered_at".
     *  - direction: "asc" (default) or "desc".
     *  - per_page:  page size (default 15, max 100).
     */
    public function index(Request $request): JsonResponse
    {
        $sort = self::SORTABLE[$request->query('sort')] ?? self::SORTABLE['name'];
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        $couriers = Courier::query()
            ->search($request->query('search'))
            ->levels($request->query('level'))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $couriers,
            CourierResource::collection($couriers->items()),
            'Couriers retrieved successfully.',
            $request->query('search'),
        );
    }

    /**
     * Store a newly created courier in storage.
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return ApiResponse::success(
            new CourierResource($courier),
            'Courier created successfully.',
            Response::HTTP_CREATED,
        );
    }

    /**
     * Display the specified courier (all of its data).
     */
    public function show(Courier $courier): JsonResponse
    {
        return ApiResponse::success(
            new CourierResource($courier),
            'Courier retrieved successfully.',
        );
    }

    /**
     * Update the specified courier in storage.
     */
    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        $courier->update($request->validated());

        return ApiResponse::success(
            new CourierResource($courier),
            'Courier updated successfully.',
        );
    }

    /**
     * Remove the specified courier from storage.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return ApiResponse::success(null, 'Courier deleted successfully.');
    }
}
