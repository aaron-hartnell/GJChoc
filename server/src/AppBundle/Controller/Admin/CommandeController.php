<?php

namespace AppBundle\Controller\Admin;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Commande;

class CommandeController extends FOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/posts")
     */
    public function getPostsAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
    }

    /**
     * @Rest\View()
     * @Rest\Get("/admin/commandes")
     */
    public function getCommandesAction(Request $request)
    {
        $commandes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Commande')
                ->findAll();

        return $commandes;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/admin/commandes/{id}")
     */
    public function getCommandeAction(Request $request)
    {
        $commande = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Commande')
                ->find($request->get('id')); // L'identifiant en tant que paramétre n'est plus nécessaire

        if (empty($commande)) {
            return commandeNotFound();
        }

        return $commande;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/commandes")
     */
    public function postCommandesAction(Request $request)
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($commande);
            $em->flush();
            return $commande;
        } else {
            return $form;
        }
    }

    private function commandeNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Commande not found'], Response::HTTP_NOT_FOUND);
    }
}
