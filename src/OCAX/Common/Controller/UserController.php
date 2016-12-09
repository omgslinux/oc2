<?php

namespace OCAX\Common\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        return $this->render('layouts/main.html.twig', [
            'locale' => $request->getLocale(),
        ]);
    }
}
