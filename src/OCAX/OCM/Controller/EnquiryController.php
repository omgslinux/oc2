<?php

namespace OCAX\OCM\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

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
            //'dataProvider' => $model->publicSearch(),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @Route("/create", name="enquiry_create")
     * @Method("GET")
     */
    public function createAction()
    {
        $this->layout='//layouts/column1';
        $this->pageTitle=CHtml::encode(__('New enquiry'));
        $model=new Enquiry;
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_GET['budget'])) {
            $model->budget=$_GET['budget'];
            $model->type = 1;
        }

        if ($budget=Budget::model()->findByPk($model->budget)) {
            if (Config::model()->findByPk('year')->value != $budget->year) {
                $msg = __('This budget is from the year %s.').'. '.__('Do you want to continue?');
                $msg = str_replace('%s', $budget->year, $msg);
                Yii::app()->user->setFlash('prompt_year', $msg);
            }
        } else {
            Yii::app()->end();
        }

        $model->addressed_to = ADMINISTRATION;

        if (isset($_POST['Enquiry'])) {
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
        $this->render('create', array(
            'model'=>$model,
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
}
