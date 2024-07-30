<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/habitat', name: 'app_api_habitat_')]
class HabitatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/habitat",
     *     summary="Ajouter un habitat",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de l'habitat à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string", example="Jungle"),
     *              @OA\Property(property="description", type="string", example="Habitat pour animaux de la jungle"),
     *              @OA\Property(property="commentaire_habitat", type="string", example="Commentaire sur la jungle"),
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Habitat créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="nom", type="string", example="Jungle"),
     *             @OA\Property(property="description", type="string", example="Habitat pour animaux de la jungle"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="Commentaire sur la jungle"),
     *           )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

        $this->manager->persist($habitat);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($habitat, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_habitat_show',
            ['id' => $habitat->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/habitat/{id}",
     *     summary="Afficher un habitat par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'habitat à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitat trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="nom", type="string", example="Jungle"),
     *             @OA\Property(property="description", type="string", example="Habitat pour animaux de la jungle"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="Commentaire sur la jungle"),
     *           )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $responseData = $this->serializer->serialize($habitat, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/habitat/{id}",
     *     summary="Modifier un habitat par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'habitat à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="nom", type="string", example="Jungle"),
     *             @OA\Property(property="description", type="string", example="Habitat pour animaux de la jungle"),
     *             @OA\Property(property="commentaire_habitat", type="string", example="Commentaire sur la jungle"),
     *           )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat modifié avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $habitat = $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/habitat/{id}",
     *     summary="Supprimer un habitat par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'habitat à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->findOneBy(['id' => $id]);
        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
