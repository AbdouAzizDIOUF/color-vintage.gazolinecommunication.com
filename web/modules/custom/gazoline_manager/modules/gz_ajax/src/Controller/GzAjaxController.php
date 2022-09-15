<?php

namespace Drupal\gz_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\user\Entity\User;

class GzAjaxController extends ControllerBase
{
	public function newsletter() {
		$post = (object) $_POST;
		$response = [
		  'status' => FALSE,
		  'errors' => [],
		];

		// Validation des saisies.
		if (!empty($post->email) && !empty($post->rgpd) && \Drupal::service('email.validator')
				->isValid($post->email)) {
			$values = [
				'webform_id' => 'newsletter',
				'entity_type' => NULL,
				'entity_id' => NULL,
				'in_draft' => FALSE,
				'uid' => 0,
				'langcode' => 'fr',
				'data' => [
				  'email' => $post->email,
				  'rgpd' => 'Acceptez',
				],
			];
			$webform = Webform::load($values['webform_id']);

			// Si le webform est actif.
			if (WebformSubmissionForm::isOpen($webform)) {
				$errors = WebformSubmissionForm::validateFormValues($values);
				// S'il n'y a pas d'erreurs de validation.
				if (empty($errors)) {
					$submission = WebformSubmissionForm::submitFormValues($values);
					$response['sid'] = $submission->id();
					$response['status'] = TRUE;
				} else {
					// $response['errors'][] = 'Il y a eu une erreur lors de votre inscription. Réessayez plus tard.';
					foreach ($errors as $error) {
					  $response['errors'][] = $error;
					}
				}
			} else {
		    	$response['errors'][] = 'Le formulaire n\'est pas disponible actuellement. Réessayez plus tard.';
			}
		}
		else {
			if (empty($post->rgpd)) {
				$response['errors'][] = 'Veuillez accepter les RGPD.';
			}
			if (empty($post->email)) {
				$response['errors'][] = 'Veuillez saisir votre adresse mail.';
			}

			if (!empty($post->email) && !\Drupal::service('email.validator')
					->isValid($post->email)) {
				$response['errors'][] = 'Votre adresse mail n\'est pas valide.';
			}
		}

	  return new JsonResponse($response);
	}

	public function uploadUser() {

		$return = [];
		// if (($handle = fopen($_SERVER['DOCUMENT_ROOT']."/sites/default/files/upload_users/users_thermoflan.csv", "r")) !== FALSE) {
		// 	while (($data = fgetcsv($handle, 0, ";", '"')) !== FALSE) {
		// 		$name = $data[0];
		// 		$mail = $data[1];
		// 		$compagny = $data[2];
		// 		$language = \Drupal::languageManager()->getCurrentLanguage()->getId();
		// 		$ids = \Drupal::entityQuery('user')
		// 		        ->condition('name', $mail)
		// 		        ->range(0, 1)
		// 		        ->execute();
		// 	    if(!empty($ids)){
		// 	    	$user = User::load(reset($ids));
		// 			$user->set("field_raison_sociale", $compagny);
		// 			$user->set("field_nom", $name);
		// 			$user->save();
		// 	    } else {
		// 			$user = User::create();
		// 			//Mandatory settings
		// 			$user->setPassword('password');
		// 			$user->enforceIsNew();
		// 			$user->setEmail($mail);
		// 			$user->setUsername($mail);//This username must be unique and accept only a-Z,0-9, - _ @ .
		// 			//Optional settings
		// 			$user->set("init", $mail);
		// 			$user->set("langcode", $language);
		// 			$user->set("preferred_langcode", $language);
		// 			$user->set("preferred_admin_langcode", $language);
		// 			$user->addRole('client');
		// 			//custom fields user
		// 			$user->set("field_raison_sociale", $compagny);
		// 			$user->set("field_nom", $name);
		// 			//Save user
		// 			$user->save();
		// 	    }
		//     }
		//     fclose($handle);
		//     $return['finish'] = true;
		// }

		return new JsonResponse($return);
	}

}