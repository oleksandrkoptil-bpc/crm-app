<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'CRM API',
    version: '1.0.0',
    description: 'API for ticket creation and ticket statistics.'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Use the API token in the Authorization header: Bearer {token}',
)]
#[OA\SecurityScheme(
    securityScheme: 'widgetToken',
    type: 'apiKey',
    in: 'header',
    name: 'X-Widget-Token',
    description: 'Temporary token used by the embedded widget',
)]
#[OA\Schema(
    schema: 'TicketCustomer',
    required: ['id', 'name', 'phone'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'name', type: 'string', example: 'John Smith'),
        new OA\Property(property: 'phone', type: 'string', example: '+380501112233'),
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'john@example.test'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TicketResource',
    required: ['id', 'subject', 'message', 'status', 'customer', 'attachments_count', 'created_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 24),
        new OA\Property(property: 'subject', type: 'string', example: 'Payment issue'),
        new OA\Property(property: 'message', type: 'string', example: 'Customer cannot complete payment.'),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'processed'], example: 'new'),
        new OA\Property(property: 'customer', ref: '#/components/schemas/TicketCustomer'),
        new OA\Property(property: 'attachments_count', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-05-14T14:45:00Z'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TicketStoreResponse',
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/TicketResource'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TicketStatusCounters',
    properties: [
        new OA\Property(property: 'new', type: 'integer', example: 5),
        new OA\Property(property: 'in_progress', type: 'integer', example: 2),
        new OA\Property(property: 'processed', type: 'integer', example: 9),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TicketStatisticsPeriod',
    properties: [
        new OA\Property(property: 'total', type: 'integer', example: 16),
        new OA\Property(property: 'by_status', ref: '#/components/schemas/TicketStatusCounters'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'TicketStatisticsResponse',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'day', ref: '#/components/schemas/TicketStatisticsPeriod'),
                new OA\Property(property: 'week', ref: '#/components/schemas/TicketStatisticsPeriod'),
                new OA\Property(property: 'month', ref: '#/components/schemas/TicketStatisticsPeriod'),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UnauthorizedResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized.'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
            type: 'object',
        ),
    ],
    type: 'object',
)]
class OpenApiSpec
{
}
