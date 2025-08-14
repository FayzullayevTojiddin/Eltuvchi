<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Eltuvchi API",
 *     version="1.0.0",
 *     description="Eltuvchi platformasi uchun API hujjatlari"
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local server"
 * )
 *
 * // Sanctum Bearer token uchun global security scheme
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Sanctum authentication using Bearer token"
 * )
 */
class OpenApi {}