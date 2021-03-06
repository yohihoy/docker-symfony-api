<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Localization/LanguageController.php
 */

namespace App\Controller\Api\Localization;

use App\Service\LocalizationService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LanguageController
 *
 * @Route(
 *     path="/localization/language",
 *     methods={"GET"}
 *  )
 *
 * @SWG\Get(security={})
 *
 * @SWG\Tag(name="Localization")
 *
 * @package App\Controller\Api\Localization
 */
class LanguageController
{
    private LocalizationService $localization;

    /**
     * Constructor
     *
     * @param LocalizationService $localization
     */
    public function __construct(LocalizationService $localization)
    {
        $this->localization = $localization;
    }

    /**
     * Endpoint action to get supported languages. This is for use to choose
     * what language your frontend application can use within its translations.
     *
     * @SWG\Response(
     *      response=200,
     *      description="List of language strings.",
     *      @SWG\Schema(
     *          type="array",
     *          example={"en","ru"},
     *          @SWG\Items(type="string"),
     *      ),
     *  )
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->localization->getLanguages());
    }
}
