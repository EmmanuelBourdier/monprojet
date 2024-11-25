<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Pack;
use App\Entity\UserPack;
use App\Form\BuyPackType;
use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('The user is not an instance of User. Actual type: ' . get_class($user));
        }

        $userId = $user->getId();

        $userPack = $entityManager->getRepository(UserPack::class)->findOneByCredit([
            'userId' => $userId
        ]);
        $currentPack = $userPack ? $userPack->getPack() : null;

        return $this->render('account/index.html.twig', ['userPack' => $userPack,'currentPack' => $currentPack]);
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
   
    

    #[Route('/compte/utiliser-pack', name: 'app_account_use_pack')]
    public function usePack(EntityManagerInterface $entityManager): Response
    {
       
         $user = $this->getUser();
        //dd($user);

        if (!$user instanceof User) {
            throw new \LogicException('The user is not an instance of User. Actual type: ' . get_class($user));
        }

        $userId = $user->getId();
        //dd($userId);
        $userPack = $entityManager->getRepository(UserPack::class)->findOneByCredit([
            'userId' => $userId
        ]);
        //dd($userPack);

        $credit= $userPack->getCredit();
        //dd($credit);

        $quantity= $userPack->getPack()->getQuantity();
        //dd($quantity);

        if ($userPack && $credit >= 1 && $credit <= $quantity) {
            $userPack->setCredit($userPack->getCredit() -1);
            $entityManager->persist($userPack);
            $entityManager->flush();

            $this->addFlash('success', 'Pack utilisé avec succés!');
        } else {
            $this->addFlash('error', 'plus de crédit disponible, achetez un pack pour continuer');
        }

        return $this->redirectToRoute('app_account');
    }


    /* nouvelle logique plus souple */


#[Route('/compte/acheter-pack', name: 'app_account_buy_pack')]
public function buyPack(Request $request, EntityManagerInterface $entityManager): Response
{
        $user = $this->getUser();

        if (!$user instanceof User) {
           return new RedirectResponse($this->generateUrl('app_login'));
        }

        $userId = $user->getId();

        $userPack = $entityManager->getRepository(UserPack::class)->findOneByCredit($userId);

    if ($userPack) {
        {
            $this->addFlash('error', 'Vous avez déjà un pack en cours d\'utilisation');
            return $this->redirectToRoute('app_account');
        }
    }

    $form = $this->createForm(BuyPackType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $pack = $form->getData();
        $packId = $pack['pack'];
       

        // Redirect to the summary page with the packId
        return $this->redirectToRoute('app_account_pack_summary', ['packId' => $packId]);
    }

    return $this->render('account/buy_pack.html.twig', ['form' => $form->createView()]);
}

#[Route('/compte/acheter-pack/resume', name: 'app_account_pack_summary')]
public function packSummary(Request $request, EntityManagerInterface $entityManager): Response
{
    $packId = $request->query->get('packId');
    // Fetch the pack details using the packId
    $pack = $entityManager->getRepository(Pack::class)->find($packId);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }

    return $this->render('account/pack_summary.html.twig', [
        'pack' => $pack,
    ]);
}

#[Route('/compte/acheter-pack/confirmation', name: 'app_account_pack_confirm')]
public function packConfirm(Request $request, EntityManagerInterface $entityManager): Response
{
    $packId = $request->query->get('packId');
    $pack = $entityManager->getRepository(Pack::class)->find($packId);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }

    $user = $this->getUser();

   
   

    $userPack = new UserPack();
    $userPack->setPack($pack);
    $userPack->setUser($user);
    $userPack->setCredit($pack->getQuantity());

    $entityManager->persist($userPack);
    $entityManager->flush();

    $this->addFlash('success', 'Pack acheté avec succès!');

    return $this->redirectToRoute('app_account');


}

}