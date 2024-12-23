<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Repository\SettingsRepository;

#[Route('/admin/articles', name: 'admin_articles_')]
#[IsGranted('ROLE_EDITOR')]
class AdminArticleController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository(Article::class)->findAll();
        $settings = $this->settingsRepository->getSettings('general_settings');

        return $this->render('admin/article/index.html.twig', [
            'articles' => $articles,
            'headless_mode' => $settings?->isHeadlessMode() ?? false,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (empty($article->getSlug())) {
                    $slugger = new AsciiSlugger();
                    $article->setSlug(strtolower($slugger->slug($article->getTitle())));
                }

                $em->persist($article);
                $em->flush();

                $this->addFlash('success', 'Article created successfully.');
                return $this->redirectToRoute('admin_articles_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error creating article: ' . $e->getMessage());
            }
        }

        return $this->render('admin/article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Article $article): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (empty($article->getSlug())) {
                    $slugger = new AsciiSlugger();
                    $article->setSlug(strtolower($slugger->slug($article->getTitle())));
                }

                $em->flush();
                $this->addFlash('success', 'Article updated successfully.');
                return $this->redirectToRoute('admin_articles_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error updating article: ' . $e->getMessage());
            }
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article deleted successfully.');
        }

        return $this->redirectToRoute('admin_articles_index');
    }
}