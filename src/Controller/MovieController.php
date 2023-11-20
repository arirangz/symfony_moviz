<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    #[Route('/films', name: 'app_movies')]
    public function index(MovieRepository $movieRepository, Request $request): Response
    {
        $genreId = $request->get('genreId');
        $movies = $movieRepository->findMovies($genreId);

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/films/{id}', name: 'app_movie_show')]
    public function show(Movie $movie, Request $request, EntityManagerInterface $entityManager, 
                        Security $security, ReviewRepository $reviewRepository,
                        SessionInterface $session): Response
    {

        $session->set('previous_url', $request->getUri());
        $averageRate = $reviewRepository->getAverageRateByMovieId($movie->getId());

        $user = $security->getUser();

        $review = $reviewRepository->findOneBy(['movie' => $movie, 'user' => $user]);

        if (!$review) {
            $review = new Review();
            $review->setMovie($movie);
            $review->setUser($user);
            $review->setApproved(false);
        }

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'form' => $form,
            'user' => $user,
            'averageRate' => $averageRate,
        ]);
    }
}
