<?php
namespace App\Docs;

/**
 * @OA\Tag(name="Users", description="User management endpoints")
 */
class UserSwagger extends MainSwagger{
    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserFormRequest")
     *     ),
     *     @OA\Response(response=201, description="User created successfully")
     * )
     */

    public function store(){}



    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get a list of users",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page Number",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="Limit",
     *         in="query",
     *         description="Limit per page",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Sort the result By",
     *         required=false,
     *         @OA\Schema(type="string", enum={"id","role"})
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="Sort Order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc","desc"})
     *     ),
     *     @OA\Parameter(
     *         name="loadRelation",
     *         in="query",
     *         description="Choose what relation you want to load",
     *         required=false,
     *         @OA\Schema(type="string", enum={"library","privilage"})
     *     ),
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function index(){}

}