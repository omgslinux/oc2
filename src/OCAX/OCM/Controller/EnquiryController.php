<?php

namespace OCAX\OCM\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OCAX\Budget\Entity\BudgetToken as Budget;
use OCAX\OCM\Entity\Enquiry;
use OCAX\Common\Entity\AppLog;

/**
 * @Route("/enquiry", name="enquiry")
 */
class EnquiryController extends Controller
{
    /**
     * @Route("/", name="enquiry_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        //$this->layout='//layouts/column1';
        $config = $this->get('ocax.config');
        $em = $this->getDoctrine()->getManager();
        $enquiries = $em->getRepository('OCAX\OCM\Entity\Enquiry')->findAll();
        // Human states
        $enquirystates = $em->getRepository('OCAX\OCM\Entity\EnquiryState')->findAll();
        $enquiriescount = array();
        //dump($enquirystates);
        foreach ($enquirystates as $enquirystate) {
            $id = $enquirystate->getId();
            $enquirecount = $em->getRepository('OCAX\OCM\Entity\Enquiry')->findBy(array('state' => $id));
            $enquiriescount[$enquirystate->getState()] = array(
                'id' => $id,
                'state' => $enquirystate->getState(),
                'description' => $enquirystate->getDescription(),
                'count' => count($enquirecount)
            );
        }
        //$model->unsetAttributes();  // clear any default values
        /*if (isset($_GET['Enquiry'])) {
            $model->attributes=$_GET['Enquiry'];
        }
        */
/*
        if (isset($_GET['display'])) {
            $displayType = $_GET['display'];
            Yii::app()->request->cookies['enquiry_display_type'] = new CHttpCookie('enquiry_display_type', $displayType);
        } elseif (isset(Yii::app()->request->cookies['enquiry_display_type'])) {
            $displayType=Yii::app()->request->cookies['enquiry_display_type']->value;
        } else {
            $displayType='list';
        }
*/
        $displayType = 'list';
        if ($request->get('display')) {
            $displayType = $request->get('display');
            $cookie = new Cookie('enquiry_display_type', $displayType);
            $response = new Response();
            $response->headers->setCookie($cookie);
        } elseif ($request->cookies->get('enquiry_display_type')) {
            $displayType = $request->cookies->get('enquiry_display_type');
        }

        return $this->render('enquiry/index.html.twig', array(
            'title' => $this->get('translator')->trans('Enquiries') . ' '. $config->findParameter('administrationName'),
            'displayType' => $displayType,
            'enquiries' => $enquiries,
            'enquiriescount' => $enquiriescount,
            'enquirystates' => $enquirystates,
            'enquiryactive' => 'active',
            //'dataProvider' => $model->publicSearch(),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @Route("/create", name="enquiry_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request, Budget $budget = null)
    {
        //$this->layout='//layouts/column1';
        //$this->pageTitle=CHtml::encode(__('New enquiry'));
        $enquire=new Enquiry();
        //$form = $this->createForm($enquire);
        $user=$this->getUser();
        $form = $this->createForm('OCAX\OCM\Form\EnquiryType', $enquire);

        $form->handleRequest($request);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*        if (isset($_GET['budget'])) {
                        $model->budget=$_GET['budget'];
                        $model->type = 1;
                    }
            */
            /* if ($this->request->get('budget')) {
                $enquire->setBudgetary(true);
            } */
            $enquire->setCreationDate(new \DateTime());
            $enquire->setAssignDate(new \DateTime());
            $enquire->setModificationDate(new \DateTime());
            $enquire->setSubmissionDate(new \DateTime());
            $state = $em->getRepository('OCMBundle:EnquiryState')
                ->findBy(array('description' => 'ENQUIRY_PENDING_VALIDATION'));
            $enquire->setState($state);
            $user = $em->getRepository('CommonBundle:User')->find($this->getUser());
            $enquire->setUser($user);
            $body=trim(strip_tags(str_replace("<br />", " ", $enquire->getBody())));
            $enquire->setBody($body);
            if (is_null($budget)) {
                if (Config::model()->findByPk('year')->value != $budget->year) {
                    $msg = __('This budget is from the year %s.').'. '.__('Do you want to continue?');
                    $msg = str_replace('%s', $budget->year, $msg);
                    $this->user->setFlash('prompt_year', $msg);
                }
            }

            // Para obtener el id
            $em->persist();

            $description = new EnquiryText();
            $description->setEnquiry($enquire->getId());
            $description->setSubject($enquire->getSubject());
            $description->setBody=trim(strip_tags(str_replace("<br />", " ", $enquire->getBody())));

            $subscription=new EnquirySubscribe();
            $subscription->setEnquiry($enquire);
            $subscription->setUser($user);

            $log=new AppLog();
            $log->setEntity('Enquiry');
            $log->setMessage($this->get('translator')->trans('New enquiry') . 'id='.$enquire->getId());
            $log->setEntityId($enquire->getId());

            $config = $this->get('ocax.config');

            $enquiremail=new EnquiryEmail();
            $enquiremail->setSubject=$this->get('translator')->trans($enquire->getState()->getDescription);
            $enquiremail->setSender($user);
            $recipients=$user->getEmail().',';
            $message = \Swift_Message::newInstance()
                    ->setSubject($enquiremail->getSubject())
                    ->setSender($config->findParameter('emailNoReply'))
                    ->SetFrom(
                        array
                        (
                            $config->findParameter('emailNoReply'),
                            $config->findParameter('siglas')
                        )
                    )
                    ;
            $managers=$em->getRepository('CommonBundle:User')->findBy(array('manager'=> true));
            foreach ($managers as $manager) {
                $message->addBcc($manager->getEmail());
                $recipients.=' '.$manager->getEmail().',';
            }
            $template = $this->get('twig')->createTemplate($enquire->getState()->getEmailTemplates()->__toString());
            $body=$template>render(
                array(
                    $enquire->getState()->getEmailTemplates()->__toString(),
                    'enquiry' => $enquire,
                    'user' => $user
                )
            );
            $enquiremail
                ->setBody($body)
                ->setRecipients($recipients)
                ->setEnquiry($enquire);
            $message->setBody($body, 'text/html');
            //$mailer->Body=EmailTemplate::model()->findByPk($model->state)->getBody($model);


            if ($this->get('mailer')->send($message)) {
                $enquiremail->setSent=true;
                $this->addFlash('success', 'We have sent you an email');
            } else {
                $this->addFlash('success', 'Your enquiry has been registered correctly');
            }

            $em->flush();

            return $this->redirectToRoute('user_panel');
        }

/*        if ($budget=Budget::model()->findByPk($model->budget)) {
            if (Config::model()->findByPk('year')->value != $budget->year) {
                $msg = __('This budget is from the year %s.').'. '.__('Do you want to continue?');
                $msg = str_replace('%s', $budget->year, $msg);
                Yii::app()->user->setFlash('prompt_year', $msg);
            }
        } else {
            Yii::app()->end();
        }
*/
        //$model->addressed_to = ADMINISTRATION;

        /* if (isset($_POST['Enquiry'])) {
            $model->setScenario('create');
            $model->attributes=$_POST['Enquiry'];
            $model->user = Yii::app()->user->getUserID();
            $model->created = date('Y-m-d');
            $model->modified = date('c');
            $model->state = ENQUIRY_PENDING_VALIDATION;
            $model->title = htmLawed::hl($model->title, array('elements'=>'-*', 'keep_bad'=>0));
            $model->body = htmLawed::hl($model->body, array('safe'=>1, 'deny_attribute'=>'script, class, id'));

            if ($model->save()) {
                $description = new EnquiryText;
                $description->enquiry=$model->id;
                $description->title=$model->title;
                $description->body=trim(strip_tags(str_replace("<br />", " ", $model->body)));
                $description->save();

                $subscription=new EnquirySubscribe;
                $subscription->user = $model->user;
                $subscription->enquiry = $model->id;
                $subscription->save();

                $mailer = new Mailer();

                $mailer->AddAddress($model->user0->email);
                $recipients=$model->user0->email.',';


                $managers=User::model()->findAllByAttributes(array('is_manager'=>'1'));
                foreach ($managers as $manager) {
                    $mailer->AddBCC($manager->email);
                    $recipients=$recipients.' '.$manager->email.',';
                }

                $recipients = substr_replace($recipients, '', -1);

                $mailer->SetFrom(
                    Config::model()->findByPk('emailNoReply')->value,
                    Config::model()->findByPk('siglas')->value
                );
                $mailer->Subject=$model->getHumanStates($model->state);
                $mailer->Body=EmailTemplate::model()->findByPk($model->state)->getBody($model);

                $email = new Email;

                $email->created = date('c');
                $email->sender=null; //app generated email
                $email->sent_as=Config::model()->findByPk('emailNoReply')->value;
                $email->title=$mailer->Subject;
                $email->body=$mailer->Body;
                $email->recipients=$recipients;
                $email->enquiry=$model->id;

                Log::model()->write('Enquiry', __('New enquiry').'. id='.$model->id, $model->id);

                if ($mailer->send()) {
                    $email->sent=1;
                    $email->save();
                    Yii::app()->user->setFlash('success', __('We have sent you an email'));
                } else {
                    $email->sent=0;
                    $email->save();
                    Yii::app()->user->setFlash('success', __('Your enquiry has been registered correctly'));
                }
                $this->redirect(array('/user/panel'));
            }
        }
        */
        return $this->render('enquiry/create.html.twig', array(
            //'model'=>$model,
            'title' => $this->get('translator')->trans('New enquiry'),
            'form' => $form->createView(),
            'user' => $user,
            'enquiryactive' => 'active',
        ));
    }

    /**
     * @Route("/", name="enquiry_subscribe")
     * @Method({"GET", "POST"})
     */
    public function subscribeAction(Request $request)
    {
        /*if (!Yii::app()->request->isAjaxRequest) {
            Yii::app()->end();
        } */

        if (isset($_POST['enquiry']) && isset($_POST['subscribe'])) {
            $user = $this->getUser();
            /*$criteria = new CDbCriteria;
            $criteria->condition = 'enquiry = :enquiry AND user = :user';
            $criteria->params[':enquiry'] = $_POST['enquiry'];
            $criteria->params[':user'] = $user; */
            $criteria = array(
                'enquiry' => 0
            );

            $em = $this->getDoctrine()->getManager();

            $subscription = $em->getRepository('OCAX\OCM\Entity\EnquirySubscribe')->find($criteria);

            if ($model && $_POST['subscribe']=='false') {
                $model->delete();
                echo '-1';
            } elseif ($model && $_POST['subscribe']=='true') {
                // do nothing. user probably just made a comment and we automatically subscribed him
                echo '0';
            } else {
                $model=new EnquirySubscribe;
                $model->enquiry = $_POST['enquiry']; // should check if enquiry id is valid.
                $model->user = $user;
                $model->save();
                echo '1';
            }
            Yii::app()->end();
        }
        echo '0';
    }

    public function budgetDetails(Budget $Budget)
    {
        // From 'enquiry/_detailsForTeam.html.twig'
        $this->render('enquiry/_budgetDetails.html.twig', array(
            'budget' => $Budget,
        ));
    }
}
