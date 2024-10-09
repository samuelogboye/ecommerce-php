<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Ecommerce API documentation",
 *     version="1.0.0",
 *
 *     @OA\Contact(
 *         email="admin@samuelogboye.com"
 *     ),
 *
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Tag(
 *     name="Examples",
 *     description="Some example pages",
 * )
 *
 * @OA\Server(
 *     description="Localhost API server",
 *     url="http://localhost:8000/api"
 * )
 * @OA\Server(
 *     description="Localhost API server",
 *     url="http://localhost:8000/api"
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     name="X-APP-ID",
 *     securityScheme="X-APP-ID"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
