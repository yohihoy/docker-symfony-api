<?php
declare(strict_types = 1);
/**
 * /src/Rest/Traits/Actions/Logged/DeleteAction.php
 */

namespace App\Rest\Traits\Actions\Logged;

use App\Annotation\RestApiDoc;
use App\Rest\Traits\Methods\DeleteMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Swagger\Annotations as SWG;

/**
 * Trait DeleteAction
 *
 * Trait to add 'deleteAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\Rest\Traits\Actions\Logged
 */
trait DeleteAction
{
    // Traits
    use DeleteMethod;

    /**
     * Delete entity, accessible only for 'ROLE_LOGGED' users.
     *
     * @Route(
     *      "/{id}",
     *      requirements={
     *          "id" = "%app.uuid_v1_regex%",
     *      },
     *      methods={"DELETE"},
     *  )
     *
     * @Security("is_granted('ROLE_LOGGED')")
     *
     * @SWG\Response(
     *      response=200,
     *      description="deleted",
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
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
