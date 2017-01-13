<?php

namespace OCAX\Budget\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OCAX\Budget\Entity\BudgetToken;

/**
 * @Route("/budget")
 */
class BudgetController extends Controller
{
    /**
     * @Route("/", name="budget_index")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
    /**
    * Displays a particular model.
    * @Route("/show/{id}", name="budget_show")
    * @Method({"GET", "POST"})
    */
    public function showAction(BudgetToken $budget)
    {
        //$this->layout='//layouts/column1';
        //$model=$this->loadModel($id);
        //$this->pageTitle=__('Enquiry').': '.$model->title;

        return $this->render('enquiry/view.html.twig', array(
            'title' => $this->get('translator')->trans('Enquiry') . ': '. $enquiry->getSubject(),
            'enquiry' => $enquiry
        ));
    }
}
