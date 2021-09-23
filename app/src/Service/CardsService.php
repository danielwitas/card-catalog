<?php

namespace App\Service;

use App\Api\ApiProblem;
use App\Collection\CardCollection;
use App\Entity\Card;
use App\Exception\ApiProblemException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardsService
{
    private PaginatorInterface $paginator;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private const ITEMS_PER_PAGE = 3;


    public function __construct(
        PaginatorInterface     $paginator,
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator
    )
    {
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param int $id
     * @return Card
     * @throws NotFoundHttpException
     */
    public function getCard(int $id): Card
    {
        return $this->findCardById($id);
    }

    /**
     * @param $page
     * @return CardCollection
     */
    public function getPaginatedCardCollection($page): CardCollection
    {
        $page = max($page, 1);
        $qb = $this->entityManager->getRepository(Card::class)->getPaginatedCardCollection();
        $pagination = $this->paginator->paginate($qb, $page, self::ITEMS_PER_PAGE);
        return new CardCollection(
            $pagination->getItems(),
            $pagination->getTotalItemCount(),
            $pagination->getItemNumberPerPage(),
            $pagination->getCurrentPageNumber()
        );
    }

    /**
     * @param string|null $name
     * @param int|null $power
     * @return Card
     */
    public function addCard($name, $power): Card
    {
        $card = new Card();
        $card->setName($name);
        $card->setPower($power);
        $this->validateCard($card);
        $this->entityManager->persist($card);
        $this->entityManager->flush();
        return $card;
    }

    /**
     * @param int $id
     * @param string|null $name
     * @param int|null $power
     * @return Card
     */
    public function updateCard(int $id, $name, $power): Card
    {
        $card = $this->findCardById($id);
        if(isset($name)) {
            $card->setName($name);
        }
        if(isset($power)) {
            $card->setPower($power);
        }
        $this->validateCard($card);
        $this->entityManager->persist($card);
        $this->entityManager->flush();
        return $card;
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function deleteCard(int $id): void
    {
        $card = $this->findCardById($id);
        $this->entityManager->remove($card);
        $this->entityManager->flush();

    }

    public function findCardById(int $id): Card
    {
        $card = $this->entityManager->getRepository(Card::class)->find($id);
        if (!$card) {
            throw new NotFoundHttpException(sprintf(
                'Cannot find card with id %d', $id
            ));
        }
        return $card;
    }

    private function validateCard(Card $card): void
    {
        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($card);
        if (count($errors) > 0) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_VALIDATION_ERROR
            );
            $apiProblem->set('errors', $this->parseErrors($errors));
            throw new ApiProblemException($apiProblem);
        }
    }

    private function parseErrors(ConstraintViolationList $errors): array
    {
        $parsedErrors = [];
        foreach ($errors as $error) {
            $parsedErrors[] = [$error->getPropertyPath() => $error->getMessage()];
        }
        return $parsedErrors;
    }
}