<?php

declare(strict_types=1);

namespace App\Service\Personio\Api\v1;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAttendanceService
{

    public const EMPLOYEE_API_PATH = '/company/attendances';

    public const ATTENDANCE_PAGE_LIMIT = 200;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $httpRequestLogger,
        #[Autowire(param: 'app.personio.api.v1.base_uri')]
        private readonly string $personioApiBaseUri,
    ) {}

    public function fetchAttendances(DateTime $startDate, DateTime $endDate): array
    {
        return $this->_fetchAttendances($startDate, $endDate);
    }

    /**
     * @param int[] $employeeIds
     */
    public function fetchAttendancesByEmployeeIds(DateTime $startDate, DateTime $endDate, array $employeeIds): array
    {
        return $this->_fetchAttendances($startDate, $endDate, ['employees' => $employeeIds]);
    }

    protected function _fetchAttendances(DateTime $startDate, DateTime $endDate, array $options = []): array
    {
        $query = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'limit' => self::ATTENDANCE_PAGE_LIMIT
        ];

        foreach ($options as $key => $value) {
            $query[$key] = $value;
        }

        $response = $this->httpClient->request(
            'GET',
            $this->personioApiBaseUri . self::EMPLOYEE_API_PATH,
            [
                'query' => $query
            ]
        );

        $apiAttendancesResponse = json_decode($response->getContent(false));

        if (
            200 !== $response->getStatusCode() ||
            !$apiAttendancesResponse->success
        ) {
            // error, could not retrieve attendences
            $this->httpRequestLogger->error($apiAttendancesResponse->error->message);

            return [];
        }

        return $apiAttendancesResponse->data;
    }
}
