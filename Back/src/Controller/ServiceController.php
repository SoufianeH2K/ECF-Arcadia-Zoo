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
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        try {
            // Deserialize the request data to a Service entity
            $service = new Service();
            $formData = $request->files->get('service_image');
            if ($formData) {
                $fileContent = file_get_contents($formData->getPathname());
                $service->setServiceImage($fileContent);
            }

            $service->setNom($request->request->get('nom'));
            $service->setDescription($request->request->get('description'));

            // Persist the new service entity
            $entityManager->persist($service);
            $entityManager->flush();

            // Serialize the response
            $responseData = $serializer->serialize($service, 'json');
            return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Unable to save service'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            // Convert the image to Base64 for JSON response if needed
            $responseData = [
                'id' => $service->getId(),
                'nom' => $service->getNom(),
                'description' => $service->getDescription(),
                'service_image' => $service->getImageBase64(), // Include the Base64 image data
            ];

            return new JsonResponse($responseData, Response::HTTP_OK);
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
            // Handle file upload for service_image if a new one is provided
            $formData = $request->files->get('service_image');
            if ($formData) {
                $fileContent = file_get_contents($formData->getPathname());
                $service->setServiceImage($fileContent);
            }

            // Get other fields from the form data
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            // Validate that 'nom' and 'description' are provided
            if (null === $nom || null === $description) {
                return new JsonResponse(['error' => 'Nom and Description must be provided'], Response::HTTP_BAD_REQUEST);
            }

            // Log the values to check what is being set
            $this->get('logger')->info('Editing Service:', [
                'id' => $id,
                'nom' => $nom,
                'description' => $description,
            ]);

            // Update the service entity
            $service->setNom($nom);
            $service->setDescription($description);

            // Persist the updated service entity
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