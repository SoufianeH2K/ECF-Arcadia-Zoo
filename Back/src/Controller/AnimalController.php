<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/animal', name: 'app_api_animal_')]
class AnimalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AnimalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /** @OA\Post(
     *     path="/api/animal",
     *     summary="Ajouter un animal",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données de l'animal à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="prenom", type="string", example="Bambi"),
     *              @OA\Property(property="etat", type="string", example="Etat de l'animal"),
     *              @OA\Property(property="race_id", type="integer", example="1")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Animal créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", example="3"),
     *             @OA\Property(property="prenom", type="string", example="Bambi"),
     *             @OA\Property(property="etat", type="string", example="Etat de l'animal"),
     *             @OA\Property(property="race", type="object", example="id: 1, label: Labrador")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $raceId = $data['race_id'] ?? null;

        $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');

        if ($raceId) {
            $race = $this->manager->getReference('App\Entity\Race', $raceId);
            $animal->setRace($race);
        }

        $this->manager->persist($animal);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($animal, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_animal_show',
            ['id' => $animal->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/animal/{id}",
     *     summary="Afficher un animal par ID",
     *     description="Récupère les informations d'un animal spécifié par son ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'animal à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1", description="ID de l'animal"),
     *             @OA\Property(property="prenom", type="string", example="Nom de l'animal"),
     *             @OA\Property(property="etat", type="string", example="Etat de l'animal"),
     *             @OA\Property(property="race", type="object", description="Race de l'animal",
     *                 @OA\Property(property="id", type="integer", example="1", description="ID de la race"),
     *                 @OA\Property(property="nom", type="string", example="Nom de la race")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal non trouvé"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $responseData = $this->serializer->serialize($animal, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/animal/{id}",
     *     summary="Modifier un animal par ID",
     *     description="Permet de modifier les informations d'un animal spécifié par son ID, y compris son prénom, son état et sa race.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'animal à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Nouvelles informations de l'animal",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="prenom", type="string", example="Nouveau Prénom"),
     *             @OA\Property(property="etat", type="string", example="Nouvel état de l'animal"),
     *             @OA\Property(property="race_id", type="integer", example="1", description="ID de la race de l'animal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Animal modifié avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $service = $this->serializer->deserialize(
                $request->getContent(),
                Animal::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/animal/{id}",
     *     summary="Supprimer un animal par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'animal à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Animal supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $animal = $this->repository->findOneBy(['id' => $id]);
        if ($animal) {
            $this->manager->remove($animal);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
