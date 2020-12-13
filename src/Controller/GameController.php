<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\EditGameType;
use App\Form\GameType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
  /**
   * @Route("/game/{id<\d+>}", name="game_details")
   */
  public function gameDetails(Game $game): Response
  {
    return $this->render('game/details.html.twig', [
      'game' => $game,
    ]);
  }

  /**
   * @Route("/game/add", name="game_add")
   */
  public function gameForm(Request $request, EntityManagerInterface $manager): Response
  {
    $game = new Game();

    $gameForm = $this->createForm(GameType::class, $game);

    $gameForm->handleRequest($request);

    if ($gameForm->isSubmitted() && $gameForm->isValid()) {
      // enregistrement du jeu en base de données
      $game->setDateAdd(new \DateTime());
      $game->setUser($this->getUser());
      $manager->persist($game);
      $manager->flush();
      return $this->redirectToRoute('profile');
    }

    return $this->render('game/game-form.html.twig', [
      'game_form' => $gameForm->createView()
    ]);
  }

  /**
   * @Route("/game/{id}/edit", name="game_edit")
   */
  public function editGame(string $id, Request $request, EntityManagerInterface $manager): Response
  {
    $game = $manager->getRepository(Game::class)->find($id);

    if (!$game) {
      throw $this->createNotFoundException('No game found for id ' . $id);
    }

    $gameForm = $this->createForm(EditGameType::class, $game);

    $gameForm->handleRequest($request);

    if (!$gameForm->isSubmitted()) {
      return $this->render('game/game-edit-form.html.twig', [
        'game_form' => $gameForm->createView()
      ]);
    }

    $manager->persist($game);
    $manager->flush();

    return $this->redirectToRoute('profile');
  }
}
