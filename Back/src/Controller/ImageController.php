<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
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

#[Route('/api/image', name: 'app_api_image_')]
class ImageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ImageRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/image",
     *     summary="Ajouter une image",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de l'image à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="image_data", type="blob", example="Blob file")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Image ajoutée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="image_data", type="blob", example="Blob file")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $image = $this->serializer->deserialize($request->getContent(), Image::class, 'json');

        $this->manager->persist($image);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($image, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_image_show',
            ['id' => $image->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/image/{id}",
     *     summary="Afficher une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image trouvée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="image_data", type="blob", example="Blob file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);
        if ($image) {
            $responseData = $this->serializer->serialize($image, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

#[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/image/{id}",
     *     summary="Modifier une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="image_data", type="blob", example="Blob file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image modifiée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);
        if ($image) {
            $image = $this->serializer->deserialize(
                $request->getContent(),
                Image::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $image]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/image/{id}",
     *     summary="Supprimer une image par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Image supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image non trouvée"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $image = $this->repository->findOneBy(['id' => $id]);
        if ($image) {
            $this->manager->remove($image);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}

