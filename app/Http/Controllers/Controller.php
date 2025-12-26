<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Asxana API",
 *     version="1.0.0",
 *     description="Asxana loyihasi uchun REST API dokumentatsiyasi. Bu API orqali siz kategoriyalar, mahsulotlar va boshqa resurslar bilan ishlashingiz mumkin.",
 *     @OA\Contact(
 *         email="support@asxana.uz",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 */
abstract class Controller
{
    //
}
