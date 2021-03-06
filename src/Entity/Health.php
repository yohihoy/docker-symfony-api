<?php
declare(strict_types = 1);
/**
 * /src/Entity/Health.php
 */

namespace App\Entity;

use App\Entity\Interfaces\EntityInterface;
use App\Entity\Traits\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\UuidInterface;
use Swagger\Annotations as SWG;
use Throwable;

/**
 * Class Health
 *
 * @ORM\Table(
 *      name="health",
 *  )
 * @ORM\Entity()
 *
 * @package App\Entity
 */
class Health implements EntityInterface
{
    // Traits
    use Uuid;

    /**
     * @var UuidInterface
     *
     * @Groups({
     *      "Health",
     *      "Health.id",
     *  })
     *
     * @ORM\Column(
     *      name="id",
     *      type="uuid_binary_ordered_time",
     *      unique=true,
     *      nullable=false,
     *  )
     * @ORM\Id()
     *
     * @SWG\Property(type="string", format="uuid")
     */
    private UuidInterface $id;

    /**
     * @var DateTimeImmutable
     *
     * @Groups({
     *      "Health",
     *      "Health.timestamp",
     *  })
     *
     * @ORM\Column(
     *      name="timestamp",
     *      type="datetime_immutable",
     *      nullable=false,
     *  )
     */
    private DateTimeImmutable $timestamp;


    /**
     * Constructor
     *
     * @throws Throwable
     */
    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->setTimestamp(new DateTimeImmutable('now', new DateTimeZone('UTC')));
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id->toString();
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->getCreatedAt();
    }

    /**
     * @param DateTimeImmutable $timestamp
     *
     * @return Health
     */
    public function setTimestamp(DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
