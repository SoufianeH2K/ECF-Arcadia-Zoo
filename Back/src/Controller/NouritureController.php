<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Nouriture;
use App\Repository\NouritureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/nouriture', name: 'app_api_nouriture_')]
class NouritureController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private NouritureRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /** @OA\Post(
     *     path="/api/nouriture",
     *     summary="Ajouter une nourriture",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de la nourriture à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="animal_id", type="int", example="2"),
     *              @OA\Property(property="type", type="string", example="Viande"),
     *              @OA\Property(property="quantite", type="int", example="2000"),
     *              @OA\Property(property="date", type="date", example="2022-02-19"),
     *              @OA\Property(property="time", type="time", example="12:34")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Nourriture ajouté avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="animal_id", type="int", example="2"),
     *             @OA\Property(property="type", type="string", example="Viande"),
     *             @OA\Property(property="quantite", type="int", example="2000"),
     *             @OA\Property(property="date", type="date", example="2022-02-02"),
     *             @OA\Property(property="time", type="time", example="12:34")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        // Deserialize the request content into a Nouriture object without automatically setting the Animal
        $nouriture = $this->serializer->deserialize($request->getContent(), Nouriture::class, 'json', [
            'object_to_populate' => new Nouriture(),
            'ignored_attributes' => ['animal'], // Optionally ignore the animal during initial deserialization if it's included in request
        ]);

        // Assuming the request includes 'animal_id' to link with an existing Animal
        $data = json_decode($request->getContent(), true);
        $animalId = $data['animal_id'] ?? null;

        if ($animalId) {
            $animal = $this->manager->getRepository(Animal::class)->find($animalId);
            if (!$animal) {
                return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_BAD_REQUEST);
            }
            $nouriture->setAnimal($animal);
        } else {
            return new JsonResponse(['error' => 'Animal ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->manager->persist($nouriture);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($nouriture, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_nouriture_show',
            ['id' => $nouriture->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /** @OA\Get(
     *     path="/api/nouriture/{id}",
     *     summary="Afficher une nourriture par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nourriture à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nourriture trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="animal_id", type="int", example="2"),
     *             @OA\Property(property="type", type="string", example="Viande"),
     *             @OA\Property(property="quantite", type="int", example="2000"),
     *             @OA\Property(property="date", type="date", example="2022-02-02")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nourriture non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $nouriture = $this->repository->findOneBy(['id' => $id]);
        if ($nouriture) {
            $responseData = $this->serializer->serialize($nouriture, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/nouriture/{id}",
     *     summary="Modifier une nourriture par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nourriture à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="animal_id", type="int", example="2"),
     *             @OA\Property(property="type", type="string", example="Viande"),
     *             @OA\Property(property="quantite", type="int", example="2000"),
     *             @OA\Property(property="date", type="date", example="2022-02-02")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Nourriture mise à jour avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nourriture non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $nouriture = $this->repository->findOneBy(['id' => $id]);
        if ($nouriture) {
            $nouriture = $this->serializer->deserialize(
                $request->getContent(),
                Nouriture::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $nouriture]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/nouriture/{id}",
     *     summary="Supprimmer une nourriture par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nourriture à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Nourriture supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Nourriture non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $nouriture = $this->repository->findOneBy(['id' => $id]);
        if ($nouriture) {
            $this->manager->remove($nouriture);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}