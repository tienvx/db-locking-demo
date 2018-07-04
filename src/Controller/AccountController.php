<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    /**
     * @Route("/transfer/{from}/{amount}/{to}", name="transfer")
     */
    public function transfer(Request $request, string $from, int $amount, string $to)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        /** @var AccountRepository $repository */
        $repository = $entityManager->getRepository(Account::class);

        $fromAccount = $repository->findOneBy(['name' => $from]);
        if (!$fromAccount) {
            return $this->json(['message' => sprintf('There is no account by name: %s', $from)]);
        }

        $toAccount = $repository->findOneBy(['name' => $to]);
        if (!$toAccount) {
            return $this->json(['message' => sprintf('There is no account by name: %s', $to)]);
        }

        $form = $this->createFormBuilder()
            ->add('from', TextType::class, ['data' => $fromAccount->getName()])
            ->add('from_version', HiddenType::class, ['data' => $fromAccount->getVersion()])
            ->add('amount', NumberType::class, ['data' => $amount])
            ->add('to', TextType::class, ['data' => $toAccount->getName()])
            ->add('to_version', HiddenType::class, ['data' => $toAccount->getVersion()])
            ->add('save', SubmitType::class, ['label' => 'Transfer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $entityManager->lock($fromAccount, LockMode::OPTIMISTIC, $data['from_version']);
                $entityManager->lock($toAccount, LockMode::OPTIMISTIC, $data['to_version']);

            } catch(OptimisticLockException $e) {
                return $this->json(['message' => 'Sorry, can not transfer money. Please try again!']);
            }

            $fromAccount->setBalance($fromAccount->getBalance() - $amount);
            $toAccount->setBalance($toAccount->getBalance() + $amount);
            $entityManager->flush();

            return $this->redirectToRoute('success');
        }

        return $this->render('account/transfer.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return $this->render('account/success.html.twig');
    }
}
