<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    private const PER_PAGE = 5;

    public function __construct(private readonly ArticleRepository $articles)
    {
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->renderIndex(1);
    }

    #[Route('/page/{page}', name: 'home_page', methods: ['GET'], requirements: ['page' => '[1-9]\d{0,8}'])]
    public function page(int $page): Response
    {
        if (1 === $page) {
            return $this->redirectToRoute('home', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->renderIndex($page);
    }

    private function renderIndex(int $page): Response
    {
        $all = $this->articles->findAll();
        $total = \count($all);
        $pageCount = max(1, (int) ceil($total / self::PER_PAGE));

        if ($page > $pageCount) {
            throw $this->createNotFoundException('No articles found for page '.$page.'.');
        }

        return $this->render('blog/index.html.twig', [
            'featured' => 1 === $page ? $this->articles->findFeatured() : null,
            'articles' => \array_slice($all, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
            'total' => $total,
            'page' => $page,
            'page_count' => $pageCount,
        ]);
    }

    #[Route('/articles/{slug}', name: 'article', methods: ['GET'], requirements: ['slug' => '[a-z0-9-]+'])]
    public function article(string $slug): Response
    {
        $article = $this->articles->findOneBySlug($slug);
        if (null === $article) {
            throw $this->createNotFoundException('No article found for slug "'.$slug.'".');
        }

        return $this->render('blog/article.html.twig', [
            'article' => $article,
            'more' => \array_slice($this->articles->findAllExcept($slug), 0, 3),
        ]);
    }
}
