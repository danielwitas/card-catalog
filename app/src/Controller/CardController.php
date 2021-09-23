<?php

namespace App\Controller;

use App\Service\CardsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api")
 */
class CardController extends AbstractController
{
    private CardsService $cardsService;

    public function __construct(CardsService $cardsService)
    {
        $this->cardsService = $cardsService;
    }

    /**
     * @Route("/cards", name="get_card_collection", methods="GET")
     */
    public function getCardCollection(Request $request): Response
    {
        $page = $request->query->getInt('page');
        $collection = $this->cardsService->getPaginatedCardCollection($page);
        return $this->json($collection);
    }

    /**
     * @Route("/cards", name="add_card", methods="POST")
     */
    public function addCard(Request $request): Response
    {
        $content = $request->toArray();
        $name = $content['name'] ?? null;
        $power = $content['power'] ?? null;
        $response = $this->cardsService->addCard($name, $power);
        return $this->json($response, Response::HTTP_CREATED);
    }

    /**
     * @Route("/cards/{id}", name="update_card", methods="PUT")
     */
    public function updateCard(int $id, Request $request): Response
    {
        $content = $request->toArray();
        $name = $content['name'] ?? null;
        $power = $content['power'] ?? null;
        $card = $this->cardsService->updateCard($id, $name, $power);
        return $this->json($card, Response::HTTP_OK);
    }

    /**
     * @Route("/cards/{id}", name="get_card", methods="GET")
     */
    public function getCard(int $id): Response
    {
        $card = $this->cardsService->getCard($id);
        return $this->json($card);
    }

    /**
     * @Route("/cards/{id}", name="delete_card", methods="DELETE")
     */
    public function deleteCard(int $id): Response
    {
        $this->cardsService->deleteCard($id);
        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/docs", name="docs", methods="GET")
     */
    public function docs(): Response
    {
        return $this->render('docs/docs.html.twig');
    }
}

