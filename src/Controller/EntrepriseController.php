<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{


    /**
 * @Route("/add", name="entreprise_add")
 * @Route("/{id}/edit", name="entreprise_edit")
 */
 
 public function new_update(Entreprise $entreprise = null, Request $request, EntityManagerInterface $manager)
 {
    if(!$entreprise) {
        $entreprise = new Entreprise();
    }

    $form = $this->createForm(EntrepriseType::class, $entreprise);
    $form -> handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        $manager->persist($entreprise);// équivalent de prepare()
        $manager->flush();// pour valider les changements dans la base de données, il "sait" si il doit UPDATE ou INSERT et ce pour tout les objets persist()

        return $this->redirectToRoute('entreprise');
    }

    return $this->render('entreprise/add_edit.html.twig', [
        'formEntreprise'=>$form->createView(),
        'editMode'=>$entreprise->getId() !==null,
        'entreprise'=>$entreprise->getRaisonSociale(),
    ]);

 }
  /**
 * @Route("/{id}/delete", name="entreprise_delete")
 */
public function delete(Entreprise $entreprise, EntityManagerInterface $manager)
{
    foreach($entreprise->getSalaries() as $salarie){
        $entreprise->removeSalarie($salarie);
    }
$manager->remove($entreprise);
$manager->flush();

return $this->redirectToRoute('entreprise');
}

     /**
     * @Route("/entreprise", name="entreprise")
     */
    public function index()
    {
        $entreprises = $this->getDoctrine()
                ->getRepository(Entreprise::class)
                ->getAll();

        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    /**
     * @Route("entreprise/{id}", name="entreprise_show", methods="GET")
     */
    public function show(Entreprise $entreprise): Response {
        return $this->render('entreprise/show.html.twig', ['entreprise' => $entreprise]);
    }
}
