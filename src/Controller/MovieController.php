<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/films', name: 'app_movies')]
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findBy([], ['id' => 'DESC']);

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/films/{id}', name: 'app_movie_show')]
    public function show(Movie $movie): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'form' => $form
        ]);
    }
}
