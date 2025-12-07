<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Task Management API',
    description: 'Production-ready REST API for task management with authentication, CRUD operations, file attachments, and email notifications.',
    contact: new OA\Contact(
        name: 'API Support',
        email: 'support@taskapi.local'
    )
)]
#[OA\Server(
    url: 'http://localhost:8080',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter your Bearer token in the format: Bearer {token}'
)]
abstract class Controller
{
    //
}
