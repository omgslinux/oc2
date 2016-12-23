<?php

namespace OCAX\Common\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user", name="user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('layouts/main.html.twig', [
            'locale' => $request->getLocale(),
        ]);
    }

    /**
     * @Route("/panel", name="user_panel")
     */
    public function panelAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('user/panel.html.twig', [
            'locale' => $request->getLocale(),
        ]);
    }

    /**
     * Deletes a Users entity.
     *
     * @Route("/{id}/delete", name="user_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('site_index');
    }

    /**
     * Creates a form to delete a Users entity.
     *
     * @param Users $users The Users entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Smudges a User
     * The user deletes his account
     * We don't delete the database entry because it might be referenced by other models
     *
     * @Route("/{id}/optout", name="user_optout")
     * @Method({"GET", "POST"})
     */
    public function optoutAction(Request $request, User $user)
    {
        $form = $this->createOutForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //$em->remove($user);
            $user->setActive = false;
            $user->setDisabled = true;
            $user->setUsername = $this->getParameter('smudge');
            $user->setFullname = $this->getParameter('smudge');
            $user->setEmail = $this->getParameter('smudge');
            //$this->scenario = 'opt_out';
            //AppLog::model()->write('User', $this->translator('User').' '.$user->getUsername().' id='.$user->getId().' '.$this->translator('deleted account'), $user->getId());
            $em->persist($user);
            $em->flush();
        }

        return $this->redirectToRoute('site_index');
    }

    /**
     * Creates a form to optout a User entity.
     *
     * @param User $user The Users entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createOutForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_optout', array('id' => $user->getId())))
            ->setMethod('POST')
            ->getForm()
        ;
    }


    /**
     * @Route("/update", name="user_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request)
    {
        $user = $this->getUser();
        $editform = $this->createForm('OCAX\Common\Form\UserType', $user, array('require_password' => false));
        $editform->handleRequest($request);

        if ($editform->isSubmitted() && $editform->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (null != $user->getPlainpassword()) {
                $encoder = $this->get('security.password_encoder');
                $encodedPassword = $encoder->encodePassword($user, $user->getPlainpassword());
                $user->setPassword($encodedPassword);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_users_show', array('id' => $user->getId()));
        }

        return $this->render('user/update.html.twig', [
            'locale' => $request->getLocale(),
            'form' => $editform->createView(),
            'user' => $user,
        ]);
    }
}
