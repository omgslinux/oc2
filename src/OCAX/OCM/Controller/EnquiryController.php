<?php

namespace OCAX\OCM\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OCAX\Budget\Entity\BudgetToken as Budget;
use OCAX\OCM\Entity\Enquiry;
use OCAX\OCM\Entity\EnquiryText;
use OCAX\OCM\Entity\EnquiryEmail;
use OCAX\OCM\Entity\EnquirySubscribe;
use OCAX\Common\Entity\AppLog;

/**
 * @Route("/enquiry", name="enquiry_")
 */
class EnquiryController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        //$this->layout='//layouts/column1';
        $config = $this->get('ocax.config');
        $em = $this->getDoctrine()->getManager();
        if ($sort=$request->get('sort')) {
            $enquiries = $em->getRepository('OCMBundle:Enquiry')->findBy([], [ $sort => 'ASC']);
        } else {
            $enquiries = $em->getRepository('OCMBundle:Enquiry')->findAll();
        }
        dump($enquiries);
        // Human states
        $enquirystates = $em->getRepository('OCMBundle:EnquiryState')->findAll();
        $enquiriescount = array();
        foreach ($enquirystates as $enquirystate) {
            $id = $enquirystate->getId();
            $enquirecount = $em->getRepository('OCMBundle:Enquiry')->findBy(array('state' => $id));
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
     * @Route("/create", name="create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request, Budget $budget = null)
    {
        //$this->layout='//layouts/column1';
        //$this->pageTitle=CHtml::encode(__('New enquiry'));
        $em = $this->getDoctrine()->getManager();
        $enquire=new Enquiry();
        //$form = $this->createForm($enquire);
        $user=$this->getUser();
        $form = $this->createForm('OCAX\OCM\Form\EnquiryType', $enquire);

        $form->handleRequest($request);

        if ($budget = $request->get('budget')) {
            $enquire->setBudgetary(true);
            $budget=$em->getRepository('BudgetBundle:BudgetDetail')->findOneBy(array('token' => $budget));
            $enquire->setBudget($budget->getToken());
            if ($this->get('ocax.config')->findParameter('year') != $budget->getDate()->format('Y')) {
                $msg = $this->get('translator')->trans('This budget is from the year %s.').'. ';
                $msg .=$this->get('translator')->trans('Do you want to continue?');
                $msg = str_replace('%s', $budget->getDate->format('Y'), $msg);
                $this->addFlash('prompt_year', $msg);
            }
        } else {
            $enquire->setBudgetary(false);
            $enquire->setBudget($em->getRepository('BudgetBundle:BudgetToken')->find(0));
        }

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ($form->isSubmitted() && $form->isValid()) {
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
                ->findOneBy(array('state' => 'ENQUIRY_PENDING_VALIDATION'));
            $enquire->setState($state);
            $user = $em->getRepository('CommonBundle:User')->find($this->getUser());
            $enquire->setUser($user);
            $body=trim(strip_tags(str_replace("<br />", " ", $enquire->getBody())));
            $enquire->setBody($body);

            // Para obtener el id
            $em->persist($enquire);
            $em->flush($enquire);
            dump($enquire);

            $description = new EnquiryText();
            $description->setEnquiry($enquire);
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
            $enquiremail->setSubject=$this->get('translator')->trans($enquire->getState()->getDescription());
            $enquiremail->setSender($user);
            $recipients=$user->getEmail().',';
            $message = \Swift_Message::newInstance()
                    ->setSubject($enquiremail->getSubject())
                    ->setSender($config->findParameter('emailNoReply')->getValue())
                    ->SetFrom(
                        array
                        (
                            $config->findParameter('emailNoReply')->getValue() => $config->findParameter('siglas')->getValue()
                        )
                    )
                    ;
            $managers=$em->getRepository('CommonBundle:User')->findBy(array('manager'=> true));
            foreach ($managers as $manager) {
                $message->addBcc($manager->getEmail());
                $recipients.=' '.$manager->getEmail().',';
            }
            $template = $this->get('twig')->createTemplate($enquire);
            $body=$template->render(
                array(
                    $enquire->getState()->getEmailTemplates(),
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
            'create' => true,
            'enquiryactive' => 'active',
        ));
    }

    /**
     * @Route("/", name="subscribe")
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

    /**
    * Displays enquiry details for citizen
    * @Route("/show/citizen/{id}", name="_show_citizen")
    * @Method({"GET", "POST"})
    */
    public function citizenDetailsAction(Enquiry $enquiry)
    {
        $attribs = array();
        $t=$this->get('translator');
        $user=$this->getUser();
        $em = $this->getDoctrine()->getManager();
        $enquirystates=$em->getRepository('OCMBundle:EnquiryState')->findAll();

        if (!isset($hideLinks)) {
            $attribs[] = array(
                'label'=> $t->trans('Formulated'),
                'type' => 'raw',
                'value'=> $enquiry->getCreationDate()->format('d/m/Y').' '. $t->trans('by') .
                    ($enquiry->getUser() == $user || $user->isEnabled()) ?
                        $enquiry->getUser()->getFullname() :
                        CHtml::link(
                    CHtml::encode($model->user0->fullname),
                    '#!',
                    array('onclick'=>'js:getContactForm('.$model->user.');return false;')
                ),
            );
        } else {
            $attribs[] = array(
            'label'=> $t->trans('Formulated'),
            'type' => 'raw',
            'value'=> $enquiry->getCreationDate()->format('d-m-Y').' '.$t->trans('by').' '.$enquiry->getUser()->getFullname(),
            );
        }
        $attribs[] = array(
        'label'=> $t->trans('State'),
        'type' => 'raw',
        'value'=> urlencode(
            printf(
                $enquiry->getState()->getDescription(),
                ($enquiry->getAddressedTo()?'observatory':$this->get('ocax.config')->findParameter('administrationName'))
            )
        )
        //CHtml::encode($model->getHumanStates($model->state, $model->addressed_to)),
        );

        $enquirystate = $em->getRepository('OCMBundle:EnquiryState')->findOneBy(array('state' => 'ENQUIRY_AWAITING_REPLY'));
        if ($enquiry->getState()->getId() >= $enquirystate->getId()) {
            $submitted_info=$enquiry->getSubmissionDate()->format('d-m-Y').', '.$t->trans('Registry number').': '.$enquiry->getRegistryNumber();
            if ($model->documentation && !isset($hideLinks)) {
                $submitted_info = '<a href="'.$model->documentation0->getWebPath().'" target="_new">'.$submitted_info.'</a>';
            }
            $attribs[] = array(
                'label'=> $t->trans('Submitted'),
                'type'=>'raw',
                'value'=>$submitted_info,
            );
        }
        $attribs[] = array(
            'label'=>$t->trans('Type'),
            'value'=>($model->related_to) ? $model->getHumanTypes($model->type).' ('.$t->trans('reformulated').')' : $model->getHumanTypes($model->type),
        );
        $this->widget('zii.widgets.CDetailView', array(
            'id' => 'e_details',
            'cssFile' => Yii::app()->request->baseUrl.'/css/pdetailview.css',
            'data'=>$model,
            'attributes'=>$attribs,
        ));

        if ($user->isTeamMember() || $user->isManager()) {
            $enquiry_count = count($enquiry);
        } else {
            if ($user_id=Yii::app()->user->getUserID()) {
                $criteria = new CDbCriteria;
                $criteria->condition = 'budget = :budget AND user = :user';
                $criteria->params[':budget'] = $model->id;
                $criteria->params[':user'] = $user_id;
                $enquiry_count = count(Enquiry::model()->findAll($criteria));

                $criteria = new CDbCriteria;
                $criteria->condition = 'budget = :budget AND state >= :state AND NOT user = :user';
                $criteria->params[':budget'] = $model->id;
                $criteria->params[':state'] = ENQUIRY_ACCEPTED;
                $criteria->params[':user'] = $user_id;
                $enquiry_count = $enquiry_count + count(Enquiry::model()->findAll($criteria));
            } else {
                $criteria = new CDbCriteria;
                $criteria->condition = 'budget = :budget AND state >= :state';
                $criteria->params[':budget'] = $model->id;
                $criteria->params[':state'] = ENQUIRY_ACCEPTED;
                $enquiry_count = count(Enquiry::model()->findAll($criteria));
            }
        }


        if (!isset($hideBudgetDetails)) {
            if (!is_null($budget)) {
                return $this->renderPartial('_budgetDetails', array(
                    'budget'=>$budget,
                    'showLinks'=>1,
                    'showEnquiriesMadeLink'=>1,
                    'enquiry'=> $budget->getEnquiry(),
                ));
            }
        }
    }

    /**
    * Displays a particular model.
    * @Route("/show/{id}", name="show")
    * @Method({"GET", "POST"})
    */
    public function showAction(Enquiry $enquiry)
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
