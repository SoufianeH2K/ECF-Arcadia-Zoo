<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\UserRepository;
use App\Repository\AnimalRepository;
use App\Repository\RapportVeterinaireRepository;
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

#[Route('/api/rapportVeterinaire', name: 'app_api_rapport_veterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $repository,
        private UserRepository $userRepository,
        private AnimalRepository $animalRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) { }

    #[Route(methods: 'POST')]
    /**
     * @OA\Post(
     *     path="/api/rapportVeterinaire",
     *     summary="Ajouter un rapport vétérinaire",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Données du rapport vétérinaire à ajouter",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="date", type="date", example="2022-02-02"),
     *              @OA\Property(property="detail", type="string", example="Rapport de visite"),
     *              @OA\Property(property="utilisateur_id", type="integer", example="1"),
     *              @OA\Property(property="animal_id", type="integer", example="1")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Rapport vétérinaire ajouté avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="date", type="string", example="Date de creation du rapport"),
     *             @OA\Property(property="detail", type="string", example="Rapport de visite"),
     *             @OA\Property(property="utilisateur_id", type="integer", example="1"),
     *             @OA\Property(property="animal_id", type="integer", example="1")
     *         )
     *     )
     * )
     */
    public function new(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $utilisateur = $this->userRepository->find($requestData['utilisateur_id']);
        $animal = $this->animalRepository->find($requestData['animal_id']);

        $rapportVeterinaire = new RapportVeterinaire();
        $rapportVeterinaire->setDate(new \DateTime($requestData['date']));
        $rapportVeterinaire->setDetail($requestData['detail']);
        $rapportVeterinaire->setUtilisateur($utilisateur);
        $rapportVeterinaire->setAnimal($animal);

        $this->manager->persist($rapportVeterinaire);
        $this->manager->flush();

        $responseData = $serializer->serialize($rapportVeterinaire, 'json', [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'date',
                'detail',
                'utilisateur' => [
                    'id',
                ],
                'animal' => [
                    'id',
                ],
            ],
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_rapport_veterinaire_show',
            ['id' => $rapportVeterinaire->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }



    #[Route('/{id}', name: 'show', methods: 'GET')]
    /**
     * @OA\Get(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Afficher un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rapport vétérinaire trouvé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="3"),
     *             @OA\Property(property="date", type="string", example="Date de creation du rapport"),
     *             @OA\Property(property="detail", type="string", example="Rapport de visite")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    public function show(int $id, SerializerInterface $serializer): JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapportVeterinaire) {
            $responseData = $serializer->serialize($rapportVeterinaire, 'json', [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'date',
                    'detail',
                ],
            ]);

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    /**
     * @OA\Put(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Modifier un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="date", type="date", example="02/02/2022"),
     *             @OA\Property(property="detail", type="string", example="Rapport de visite")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport vétérinaire modifié avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    public function edit(int $id, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapportVeterinaire) {
            $rapportVeterinaire = $serializer->deserialize(
                $request->getContent(),
                RapportVeterinaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapportVeterinaire]
            );

            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    /**
     * @OA\Delete(
     *     path="/api/rapportVeterinaire/{id}",
     *     summary="Supprimer un rapport vétérinaire par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du rapport vétérinaire à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Rapport vétérinaire supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rapport vétérinaire non trouvé"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $rapportVeterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapportVeterinaire) {
            $this->manager->remove($rapportVeterinaire);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}