<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Product Management API',
    version: '1.0.0',
    description: 'RESTful API for managing products and categories, authenticated with Laravel Sanctum.'
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local development server'
)]
#[OA\PathItem(path: '/auth/login')]
class SwaggerController extends Controller
{
    public function index(): void {}
}
