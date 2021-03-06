<?php
declare(strict_types = 1);
/**
 * /src/Rest/Interfaces/ResponseHandlerInterface.php
 */

namespace App\Rest\Interfaces;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Interface ResponseHandlerInterface
 *
 * @package App\Rest
 */
interface ResponseHandlerInterface
{
    /**
     * Constants for response output formats.
     */
    public const FORMAT_JSON = 'json';
    public const FORMAT_XML = 'xml';

    /**
     * Constructor
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer);

    /**
     * Getter for serializer
     *
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface;

    /**
     * Helper method to get serialization context for request.
     *
     * @param Request                    $request
     * @param RestResourceInterface|null $restResource
     *
     * @return array
     */
    public function getSerializeContext(Request $request, ?RestResourceInterface $restResource = null): array;

    /**
     * Helper method to create response for request.
     *
     * @param Request                    $request
     * @param mixed                      $data
     * @param RestResourceInterface|null $restResource
     * @param int|null                   $httpStatus
     * @param string|null                $format
     * @param array|null                 $context
     *
     * @throws HttpException
     *
     * @return Response
     */
    public function createResponse(
        Request $request,
        $data,
        ?RestResourceInterface $restResource = null,
        ?int $httpStatus = null,
        ?string $format = null,
        ?array $context = null
    ): Response;

    /**
     * Method to handle form errors.
     *
     * @param FormInterface $form
     *
     * @throws HttpException
     */
    public function handleFormError(FormInterface $form): void;
}
