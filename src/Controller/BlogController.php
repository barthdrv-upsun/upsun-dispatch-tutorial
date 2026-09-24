<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blog\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(private readonly ArticleRepository $articles)
    {
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(#[MapQueryParameter] ?string $category = null): Response
    {
        $categories = $this->articles->findCategories();
        if (null !== $category && !\in_array($category, $categories, true)) {
            throw $this->createNotFoundException('No category named "'.$category.'".');
        }

        return $this->render('blog/index.html.twig', [
            'featured' => null === $category ? $this->articles->findFeatured() : null,
            'articles' => $this->articles->findByCategory($category),
            'categories' => $categories,
            'current_category' => $category,
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
