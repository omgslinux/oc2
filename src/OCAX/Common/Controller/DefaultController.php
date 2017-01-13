<?php

namespace OCAX\Common\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($wallpapers = $em->getRepository('OCMBundle:File')->findBy(array('entity'=>'wallpaper'))) {
            $images=array();
            foreach ($wallpapers as $wallpaper) {
                $images[]=$wallpaper->getPath();
            }
        } else {
            $files=array();
            $images=array();
            $dir = $this->getParameter('kernel.root_dir').'/../web/themes/default/wallpaper/';
            $files = glob($dir.'*.jpg', (real)GLOB_BRACE);

            foreach ($files as $image) {
                $images[] = '/themes/default/wallpaper/'.basename($image);
            }
            dump($dir);
        }
        shuffle($images);

        $page=$em->getRepository('CMSBundle:IntroPage')->findBy(array('published' => '1'));

        return $this->render('site/index.html.twig', [
            'locale' => $request->getLocale(),
            'images' => $images,
            'wallpapers' => json_encode($images),
            'page' => $page,
            'budgetactive' => false,
            'enquiryactive' => false,
            'fbURL' => false,
            'twURL' => false,
        ]);
    }

    /**
     * Creates a new Funds entity.
     *
     * @Route("/new", name="manage_funds_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $fund = new Funds();
        $form = $this->createForm('AppBundle\Form\FundsType', $fund);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fund);
            $em->flush();

            return $this->redirectToRoute('manage_funds_index');
        }

        return $this->render('default/edit.html.twig', array(
            'fund' => $fund,
            'action' => 'Crear fondo ',
            'backlink' => $this->generateUrl('manage_funds_index'),
            'backmessage' => 'Volver al listado',
            'create_form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Funds entity.
     *
     * @Route("/{id}/edit", name="manage_funds_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Funds $fund)
    {
        $deleteForm = $this->createDeleteForm($fund);
        $editForm = $this->createForm('AppBundle\Form\FundsType', $fund);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fund);
            $em->flush();

            return $this->redirectToRoute('manage_funds_show', array('id' => $fund->getId()));
        }

        return $this->render('default/edit.html.twig', array(
            'fund' => $fund,
            'action' => 'Editar fondo',
            'backlink' => $this->generateUrl('manage_funds_show', array('id' => $fund->getId())),
            'backmessage' => 'Volver al fondo',
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Funds entity.
     *
     * @Route("/{id}", name="manage_funds_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Funds $fund)
    {
        $form = $this->createDeleteForm($fund);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($fund);
            $em->flush();
        }

        return $this->redirectToRoute('manage_funds_index');
    }

    /**
     * Creates a form to delete a Funds entity.
     *
     * @param Funds $fund The Funds entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Funds $fund)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manage_funds_delete', array('id' => $fund->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing Funds entity.
     *
     * @Route("/entities", name="manage_funds_edit")
     * @Method({"GET", "POST"})
     */
    public function entitiesAction(Request $request)
    {
        $entities = array();
        $em = $this->getDoctrine()->getManager();

        $namespaces = $em->getConfiguration()->getEntityNamespaces();

        foreach ($namespaces as $namespace) {
            print "$namespace\n<br>";
        }
        return;
    }
}
