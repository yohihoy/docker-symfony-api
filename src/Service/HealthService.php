<?php
declare(strict_types = 1);
/**
 * /src/Service/HealthService.php
 */

namespace App\Service;

use App\Entity\Health;
use App\Repository\HealthRepository;
use Throwable;

/**
 * Class HealthService
 *
 * @package App\Service
 */
final class HealthService
{
    private HealthRepository $repository;

    /**
     * Constructor
     *
     * @param HealthRepository $repository
     */
    public function __construct(HealthRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Method to check that "all" is ok within our application. This will try to do following:
     *  1) Remove data from database
     *  2) Create data to database
     *  3) Read data from database
     *
     * These steps should make sure that at least application database is working as expected.
     *
     * @return Health|null
     *
     * @throws Throwable
     */
    public function check(): ?Health
    {
        $this->repository->cleanup();
        $this->repository->create();

        return $this->repository->read();
    }
}
