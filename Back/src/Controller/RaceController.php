<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/race', name: 'app_api_race_')]
class RaceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RaceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/race",
     *     summary="Ajouter une race",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de la race à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="label", type="string", example="Labrador")
     *              )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Race ajoutée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="label", type="string", example="Labrador"))
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $race = $this->serializer->deserialize($request->getContent(), Race::class, 'json');

        $this->manager->persist($race);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($race, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_race_show',
            ['id' => $race->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/race/{id}",
     *     summary="Afficher une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la race à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Race trouvée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="label", type="string", example="Labrador"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);
        if ($race) {
            $responseData = $this->serializer->serialize($race, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/race/{id}",
     *     summary="Modifier une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la race à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="label", type="string", example="Labrador"))
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Race modifiée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);
        if ($race) {
            $race = $this->serializer->deserialize(
                $request->getContent(),
                Race::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $race]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/race/{id}",
     *     summary="Supprimer une race par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la race à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Race supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Race non trouvée"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $race = $this->repository->findOneBy(['id' => $id]);
        if ($race) {
            $this->manager->remove($race);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
