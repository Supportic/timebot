<?php

declare(strict_types=1);

namespace App\Service\Personio\Api\v1;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiEmployeeService
{

    public const EMPLOYEE_API_PATH = '/company/employees';
    public const EMPLOYEE_PAGE_LIMIT = 100;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire(param: 'app.personio.api.v1.base_uri')]
        private readonly string $personioApiBaseUri,
    ) {}

    public function fetchEmployees(): array
    {

        $response = $this->httpClient->request(
            'GET',
            $this->personioApiBaseUri . self::EMPLOYEE_API_PATH,
            [
                'query' => [
                    'limit' => self::EMPLOYEE_PAGE_LIMIT
                ]
            ]
        );

        $apiEmployeesResponse = json_decode($response->getContent(false));

        if (200 !== $response->getStatusCode() || !$apiEmployeesResponse->success) {
            // error, could not retrieve employees

            return [];
        }

        return $apiEmployeesResponse->data;
    }

    public function fetchEmployee(int $id): ?array
    {

        $response = $this->httpClient->request(
            'GET',
            $this->personioApiBaseUri . self::EMPLOYEE_API_PATH,
            [
                'query' => [
                    'employee_id' => $id
                ]
            ]
        );

        $apiEmployeeResponse = json_decode($response->getContent(false));

        if (200 !== $response->getStatusCode() || !$apiEmployeeResponse->success) {
            // error, could not retrieve employees

            return null;
        }

        return $apiEmployeeResponse->data;
    }

    public function fetchEmployeeAbsencesBalance(int $id): array
    {

        $response = $this->httpClient->request(
            'GET',
            $this->personioApiBaseUri . self::EMPLOYEE_API_PATH,
            [
                'query' => [
                    'employee_id' => $id
                ]
            ]
        );

        $apiEmployeeAbsenceBalanceResponse = json_decode($response->getContent(false));

        if (
            200 !== $response->getStatusCode() || !$apiEmployeeAbsenceBalanceResponse->success
        ) {
            // error, could not retrieve employees

            return [];
        }

        return $apiEmployeeAbsenceBalanceResponse->data;
    }
}
