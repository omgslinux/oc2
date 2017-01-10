<?php

namespace OCAX\CMS\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use OCAX\Common\Entity\User;
use OCAX\Common\Entity\AppLog;

/**
 * @Route("/site")
 */
class SiteController extends Controller
{
    /**
     * @Route("/", name="site_index")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/login", name="site_login")
     */
    public function loginAction(Request $request)
    {
        $authChecker = $this->get('security.authorization_checker');
        $tokenStorage = $this->get('security.token_storage');

        if ($authChecker->isGranted('IS_FULLY_AUTHENTICATED')) {
            $this->redirectToRoute('user_panel');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('site/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'withCaptcha' => false,
        ));

/*
		$model=new LoginForm;
        if (Yii::app()->user->getState('attempts-login') > 3) { //make the captcha required if the unsuccessful attemps are more of thee
            $model->scenario = 'withCaptcha';
        }
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
*/
    }

    /**
     * @Route("/register", name="site_register")
     * @Method({"GET", "POST"})
     */
    public function registerAction(Request $request)
    {
        $authChecker = $this->get('security.authorization_checker');
        $tokenStorage = $this->get('security.token_storage');

        if ($authChecker->isGranted('IS_FULLY_AUTHENTICATED')) {
            $this->redirectToRoute('user_panel');
        }

        $user = new User;
        $createForm = $this->createForm('OCAX\Common\Form\UserType', $user);
        $createForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('OCAX\Common\Entity\User')->findAll();

        if ($createForm->isSubmitted() && $createForm->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encodedPassword = $encoder->encodePassword($user, $user->getPlainpassword());
            $user->setPassword($encodedPassword);
            $user->setJoined(new \DateTime());
            // First user will be admin
            if (count($users) < 1) {
                $user->setAdmin(true);
            }
            $em->persist($user);
            $em->flush();
            $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            $this->sendWelcomeText($user);

            return $this->redirectToRoute('user_panel');
        }
        return $this->render('site/register.html.twig', [
            'user' => $user,
            'create_form' => $createForm->createView(),
        ]);

/*
        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax']==='login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
*/
/*
            if ($model->validate() && $newUser->save()) {
                Log::model()->write('User', __('New user').' "'.$newUser->username.'" id='.$newUser->id, $newUser->id);
                //if want to go login, just uncomment this below
                $identity=new UserIdentity($newUser->username, $model->password);
                //$identity->authenticate();
                Yii::app()->user->login($identity, 0);
                $this->actionSendWelcomeText();
            }
*/
    }


    /**
     * Request a new password
     *
     * @Route("/requestnewpassword", name="site_request_newpassword")
     * @Method({"GET", "POST"})
     */
    public function requestNewPasswordAction(Request $request)
    {
        if (!Yii::app()->request->isAjaxRequest) {
            Yii::app()->end();
        }
        if (isset($_POST['email'])) {
            $email = htmLawed::hl(trim($_POST['email']), array('elements'=>'-*', 'keep_bad'=>0));
            if (filter_var($email, FILTER_VALIDATE_EMAIL) && $user = User::model()->findByAttributes(array('email'=>$email))) {
                if ($user->is_disabled) {
                    echo '<span style="color:red">'.__('Invalid email address').'.</span>';
                    Yii::app()->end();
                }
                $reset = new ResetPassword;
                $reset->user=$user->id;
                $reset->created = date('c');
                $reset->createCode();
                $reset->used=0;

                $link=Yii::app()->createAbsoluteUrl('site/resetPassword', array('reset'=>$reset->code));
                $link='<a href="'.$link.'">'.$link.'</a>';

                $mailer = new Mailer();
                $mailer->AddAddress($user->email);
                $mailer->SetFrom(Config::model()->findByPk('emailNoReply')->value, Config::model()->findByPk('siglas')->value);
                $mailer->Subject=__('New password request');

                $mailer->Body='<p><p>'.__('Hello').' '.$user->fullname.',</p>';
                $mailer->Body=$mailer->Body.'<p>'.Config::model()->findByPk('siglas')->value.' '.__('has received a request to reset your password').'.<br />';
                $mailer->Body=$mailer->Body.__('If you have not forgotten your password, please ignore this email').'.</p>';
                $mailer->Body=$mailer->Body.'<p>'.__('To reset your password, follow this link').'<br />'.$link.'</p>';
                $mailer->Body=$mailer->Body.'<p>'.__('Kind regards').',<br />'.Config::model()->getObservatoryName().'</p></p>';

                if ($mailer->send()) {
                    $reset->save();
                    echo '<span style="color:green">'.__('We\'ve just sent you an email').'.</span>';
                } else {
                    echo '<span style="color:red">'.$mailer->ErrorInfo.'</span>';
                }
            } else {
                echo '<span style="color:red">'.__('Sorry, we cannot find you on our database').'.</span>';
            }
        } else {
            echo '<span style="color:red">Email missing.</span>';
        }
    }

    public function sendWelcomeText(User $user)
    {
//        if(Yii::app()->user->isGuest) // add accessRules() to $this controller instead?
//			Yii::app()->end();

        $message = \Swift_Message::newInstance()
            ->setSubject('Welcome to the %ObservatoryName%')
            ->setTo('ocax@gmail.com')
            ->SetTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'site/mail/welcome.html.twig',
                    array(
                        'ObservatoryName' => 'fdh',
                        'user' => $user
                    )
                ),
                'text/html'
            );
        return;

        if ($this->get('mailer')->send($message)) {
//			Yii::app()->user->setFlash('success',__('We sent you an email'));
            $this->render('ds');
        } else {
//			Yii::app()->user->setFlash('newActivationCodeError',__('Error while sending email').'<br />'.$mailer->ErrorInfo);
            return $this->render('gf');
        }
    }
}
