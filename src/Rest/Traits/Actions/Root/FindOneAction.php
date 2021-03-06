<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Root/FindOneAction.php
 */

namespace App\Rest\Traits\Actions\Root;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\FindOneMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait FindOneAction
 *
 * Trait to add 'findOneAction' for REST controllers for 'ROLE_ROOT' users.
 *
 * @see \App\Rest\Traits\Methods\FindOneMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Root
 */
trait FindOneAction
{
    // Traits
    use FindOneMethod;

    /**
     * Find entity, accessible only for 'ROLE_ROOT' users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"GET"},
     *  )
     *
     * @Security("is_granted('ROLE_ROOT')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="success",
     *      @SWG\Schema(
     *          type="object",
     *          example={},
     *      ),
     *  )
     * @SWG\Response(
     *      response=403,
     *      description="Access denied",
     *      examples={
     *          "Access denied": "{code: 403, message: 'Access denied'}",
     *      },
     *      @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="code", type="integer", description="Error code"),
     *          @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @RestApiDoc()
     *
     * @param Request $request
     * @param string  $id
     *
     * @throws Throwable
     *
     * @return Response
     */
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
