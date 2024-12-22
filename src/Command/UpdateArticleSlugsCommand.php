<?php

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(
    name: 'app:update-article-slugs',
    description: 'Updates slugs for all articles.',
)]
class UpdateArticleSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slugger = new AsciiSlugger();

        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $updatedCount = 0;
        $usedSlugs = [];

        foreach ($articles as $article) {
            if (empty($article->getTitle())) {
                continue;
            }

            $baseSlug = strtolower($slugger->slug($article->getTitle()));
            $slug = $baseSlug;
            $counter = 1;

            // Ensure unique slug
            while (in_array($slug, $usedSlugs)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $article->setSlug($slug);
            $usedSlugs[] = $slug;
            $updatedCount++;
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
            $io->success(sprintf('Updated slugs for %d articles.', $updatedCount));
        } else {
            $io->info('No articles found to update.');
        }

        return Command::SUCCESS;
    }
} 