<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Abonnement;
use App\Entity\UserAbonnement;
use App\Form\BuyAbonnementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use DateTimeImmutable;

class AbonnementController extends AbstractController
{

    #[Route('/utiliser-abonnement', name: 'app_abonnement_use')]
    public function useAbonnement(EntityManagerInterface $entityManager): Response
    {
       
         $user = $this->getUser();
        //dd($user);

        if (!$user instanceof User) {
            throw new \LogicException('The user is not an instance of User. Actual type: ' . get_class($user));
        }

        $userId = $user->getId();
        //dd($userId);
        $userAbonnement = $entityManager->getRepository(UserAbonnement::class)->findOneByTime([
            'userId' => $userId
        ]);
        //dd($userPack);

       
        $finishedAt= $userAbonnement->getFinishedAt();
        //dd($quantity);
        $currentDate= new DateTimeImmutable();
        if ($userAbonnement && $currentDate > $finishedAt) {
            $this->addFlash('error', 'L \'abonnement est terminé veuillez le renouveler pour continuer');
            
            
        } else {
            $this->addFlash('success', 'Abonnement utilisé avec succés!');
        }

        return $this->redirectToRoute('app_account');
    }


    /* nouvelle logique plus souple */


#[Route('/acheter-abonnement', name: 'app_abonnement_buy')]
public function buyPack(Request $request, EntityManagerInterface $entityManager,SessionInterface $session): Response
{
        $user = $this->getUser();

        if (!$user instanceof User) {
            $session->set('_security.main.target_path', $this->generateUrl('app_abonnement_buy'));

           return new RedirectResponse($this->generateUrl('app_login'));
        }

        $userId = $user->getId();

        $userAbonnement = $entityManager->getRepository(UserAbonnement::class)->findOneByTime($userId);

    if ($userAbonnement) {
        {
            $this->addFlash('error', 'Vous avez déjà un Abonnement en cours d\'utilisation');
            return $this->redirectToRoute('app_account');
        }
    }

    $form = $this->createForm(BuyAbonnementType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $abonnement = $form->getData();
        $abonnementId = $abonnement['abonnement'];
       

        // Redirect to the summary page with the packId
        return $this->redirectToRoute('app_abonnement_summary', ['abonnementId' => $abonnementId]);
    }

    return $this->render('abonnement/buy_abonnement.html.twig', ['form' => $form->createView()]);
}

#[Route('/acheter-abonnement/resume', name: 'app_abonnement_summary')]
public function abonnementSummary(Request $request, EntityManagerInterface $entityManager): Response
{
    $abonnementId = $request->query->get('abonnementId');
    // Fetch the pack details using the packId
    $abonnement = $entityManager->getRepository(Abonnement::class)->find($abonnementId);

    if (!$abonnement) {
        throw $this->createNotFoundException('abonnement n\'existe pas');
    }

    return $this->render('abonnement/abonnement_summary.html.twig', [
        'abonnement' => $abonnement,
    ]);
}

#[Route('/acheter-pack/confirmation', name: 'app_pack_confirm')]
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
