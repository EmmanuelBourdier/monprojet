<?php

namespace App\Controller;

use App\Entity\UserPack;
use App\Form\BuyPackType;
use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

     #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {

        $user= $this->getUser();
        $form = $this->createForm(PasswordUserType::class, $user, ['passwordHasher' => $passwordHasher]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/password.html.twig', ['modifyPwdForm' => $form->createView()]);
    }
   


    #[Route('/compte/acheter-pack', name: 'app_account_buy_pack')]
    public function buyPack(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuyPackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pack = $form->get('pack')->getData();
            $user = $this->getUser();

            $userPack = new UserPack();
            $userPack->setUser($user);
            $userPack->setPack($pack);
          

            $entityManager->persist($userPack);
            $entityManager->flush();

            $this->addFlash('success', 'Pack acheté !');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/buy_pack.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   
    

    #[Route('/compte/utiliser-pack', name: 'app_account_use_pack')]
    public function usePack(Request $request, EntityManagerInterface $entityManager): Response
    {
        $packId = $request->request->get('pack_id');
        $user = $this->getUser();

        $userPack = $entityManager->getRepository(UserPack::class)->findOneBy([
            'user' => $user,
            'pack' => $packId,
        ]);

        if ($userPack && $userPack->getUsedQuantity() < $userPack->getPack()->getQuantity()) {
            $userPack->setUsedQuantity($userPack->getUsedQuantity() + 1);
            $entityManager->persist($userPack);
            $entityManager->flush();

            $this->addFlash('success', 'Pack used successfully!');
        } else {
            $this->addFlash('error', 'No more packs available to use!');
        }

        return $this->redirectToRoute('app_account');
    }
}
