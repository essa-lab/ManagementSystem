<?php
namespace App\Docs\Schemas;

/**
 * @OA\Schema(
 *     schema="UserFormRequest",
 *     required={"name", "email", "password", "status", "library_id", "privilages", "role"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *     @OA\Property(property="password", type="string", format="password", minLength=8, example="SecurePass123"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="profile_picture", type="string", nullable=true, example="https://example.com/profile.jpg"),
 *     @OA\Property(property="locale", type="string", enum={"en", "ar", "ku"}, nullable=true, example="en"),
 *     @OA\Property(property="library_id", type="integer", example=1),
 *     @OA\Property(
 *         property="privilages",
 *         type="array",
 *         @OA\Items(type="integer", example=5)
 *     ),
 *     @OA\Property(property="role", type="string", enum={"staff", "admin"}, example="admin")
 * )
 */
