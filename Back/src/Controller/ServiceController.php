<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/service', name: 'app_api_service_')]
class ServiceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ServiceRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /** @OA\Post(
     *     path="/api/service",
     *     summary="Ajouter un service",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données du service à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="nom", type="string", example="nom du service"),
     *              @OA\Property(property="description", type="string", example="Description du service"),
     *              @OA\Property(property="service_image", type="blob", example="Blob file")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Service créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Nom du service"),
     *             @OA\Property(property="description", type="string", example="Description du service"),
     *             @OA\Property(property="service_image", type="blob", example="Blob file")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $service = $this->serializer->deserialize($request->getContent(), Service::class, 'json');

        $this->manager->persist($service);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($service, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_service_show',
            ['id' => $service->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /** @OA\Get(
     *     path="/api/service/{id}",
     *     summary="Afficher un service par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du service à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurant trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Nom du service"),
     *             @OA\Property(property="description", type="string", example="Description du service"),
     *             @OA\Property(property="service_image", type="blob", example="Blob file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Restaurant non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);
        if ($service) {
            $responseData = $this->serializer->serialize($service, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/service/{id}",
     *     summary="Modifier un service par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du service à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string", example="nom du service"),
     *             @OA\Property(property="description", type="string", example="Description du service"),
     *             @OA\Property(property="service_image", type="blob", example="Blob file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Service mis à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);
        if ($service) {
            $service = $this->serializer->deserialize(
                $request->getContent(),
                Service::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $service]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/service/{id}",
     *     summary="Supprimer un service par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du service à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Service supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $service = $this->repository->findOneBy(['id' => $id]);
        if ($service) {
            $this->manager->remove($service);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}