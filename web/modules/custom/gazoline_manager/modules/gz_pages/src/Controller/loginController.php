<?php
/**
 * @file
 * Contains Drupal\gz_pages\Controller.
 */

namespace Drupal\gz_pages\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;


class loginController extends ControllerBase {

    public function checkMail(Request $request){
        $mail = $request->request->all()['mail'];
        $user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail'=>$mail]);
        if (count($user)>0){
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }

    public function checkUserName(Request $request)
    {
        $username = $request->request->all()['username'];
        $user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name'=>$username]);
        if (count($user)>0){
            return new JsonResponse(true);
        }
        return new JsonResponse(false);
    }

    public function content() {
		/*if (!\Drupal::currentUser()->isAnonymous()) {
				return new \Symfony\Component\HttpFoundation\RedirectResponse('/admin');
		}*/
		$error = isset($_GET['error']) ? $_GET['error'] : 0;
		$status = isset($_GET['status']) ? $_GET['status'] : NULL;

		$form_login = \Drupal::formBuilder()->getForm('Drupal\user\Form\UserLoginForm');
		//$form_register = \Drupal::formBuilder()->getForm('Drupal\user\RegisterForm');
		$entity = \Drupal::service('entity_type.manager')
		  ->getStorage('user')
		  ->create(array());



		$formObject = \Drupal::service('entity_type.manager')
		  ->getFormObject('user', 'register')
		  ->setEntity($entity);



		$form_register = \Drupal::formBuilder()->getForm($formObject);

		$forgot_password = \Drupal::formBuilder()->getForm('Drupal\user\Form\UserPasswordForm');

		$noindex = array(
            '#tag' => 'meta',
            '#attributes' => array(
                'property' => 'robots',
                'content' => 'noindex',
            ),
        );

		return array(
	      '#theme' => 'login',
	      '#attached' => array('html_head' => array(array($noindex, 'noindex'))),
	      '#error' => $error,
	      '#status' => $status,
	      '#user_login_form' => $form_login,
	      '#register_form' => $form_register,
	      '#forgot_password_form' => $forgot_password,
	    );
	} 
}
 