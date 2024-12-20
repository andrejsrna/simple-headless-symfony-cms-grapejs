<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/articles', name: 'api_articles_')]
class ArticleController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ArticleRepository $articleRepository, SerializerInterface $serializer): JsonResponse
    {
        $articles = $articleRepository->findAll();
        $json = $serializer->serialize($articles, 'json', ['groups' => ['article:read']]);
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Article $article, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($article, 'json', ['groups' => ['article:read']]);
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $article = $serializer->deserialize($request->getContent(), Article::class, 'json');
        
        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $em->persist($article);
        $em->flush();
        
        $json = $serializer->serialize($article, 'json', ['groups' => ['article:read']]);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Article $article, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $serializer->deserialize(
            $request->getContent(), 
            Article::class, 
            'json', 
            ['object_to_populate' => $article]
        );
        
        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $em->flush();
        
        $json = $serializer->serialize($article, 'json', ['groups' => ['article:read']]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Article $article, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($article);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
} 