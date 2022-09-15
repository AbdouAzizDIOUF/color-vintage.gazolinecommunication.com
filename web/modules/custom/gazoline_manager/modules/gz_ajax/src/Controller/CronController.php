<?php

namespace Drupal\gz_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\paragraphs\Entity\Paragraph;
use \Drupal\node\Entity\Node;
use \Drupal\user\Entity\User;
use mikehaertl\wkhtmlto\Pdf;

class CronController extends ControllerBase
{
	public function alertDaily() {
		$response = array();
		$today = new \DateTime();
		$tomorrow = new \DateTime();
		$tomorrow->add(new \DateInterval('P1D'));
		$nids_formations = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'formations')
		    ->condition('field_sessions.entity.field_dates.value', $today->format('Y-m-d').'T23:59:59', '>')
		    ->condition('field_sessions.entity.field_dates.value', $tomorrow->format('Y-m-d').'T23:59:59', '<=')
		    ->execute();

		$nids_order = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'commande')/*CONTENT TYPE ? panier OR commande ?*/
		    // ->condition('field_statut_commande', 15)
		    ->condition('field_mes_produits.entity.field_kits.target_id', $nids_formations, 'IN')
		    ->condition('field_mes_produits.entity.field_dates.value', $today->format('Y-m-d').'T23:59:59', '>')
		    ->condition('field_mes_produits.entity.field_dates.value', $tomorrow->format('Y-m-d').'T23:59:59', '<=')
		    ->execute();
		foreach ($nids_order as $order_key => $order_id) {
			$order = Node::Load($order_id);
			$user = User::load($order->get('field_client')->target_id);
			$user_mail = $user->getEmail();
			$user_lastname = $user->get('field_nom')->value;
			$user_firstname = $user->get('field_prenom')->value;
			$user_phone = $user->get('field_telephone')->value;
			$user_city = $user->get('field_ville')->value;
			$user_zipcode = $user->get('field_code_postale')->value;
			$user_address = $user->get('field_adresse')->value;
			$user_address_sup = $user->get('field_complement_adresse')->value;

			$mailManager = \Drupal::service('plugin.manager.mail');
			$module = 'gz_ajax';
			$key = 'send_alert_daily';
			$to = $user_mail;

			$params['message'] = 'Votre formation est demain !';

			$params['headers'] = [
				'Content-Type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
				'Reply-To' => $user_mail,
				'Bcc' => $user_mail,
			];

			$langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
			$send = true;
			$result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
		}

		return new JsonResponse($result);
	}

	public function alertHourly() {
		$response = array();
		$now = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'), new \DateTimeZone('UTC'));
		// $now->setTimezone(new \DateTimeZone('Europe/Paris'));
		$in_hour = \DateTime::createFromFormat('Y-m-d', date('Y-m-d'), new \DateTimeZone('UTC'));
		// $in_hour->setTimezone(new \DateTimeZone('Europe/Paris'));
		$in_hour->add(new \DateInterval('PT2H'));
		$nids_formations = \Drupal::entityQuery('node')
		    ->condition('status', 1)
		    ->condition('type', 'formations')
		    ->condition('field_sessions.entity.field_dates.value', $now->format('Y-m-d\TH').':00:00', '>')
		    ->condition('field_sessions.entity.field_dates.value', $in_hour->format('Y-m-d\TH').':00:00', '<')
		    ->execute();
		if($nids_formations) {
			// foreach ($nids_formations as $order_key => $order_id) {
			// 	$order = Node::Load($order_id);
			// 	$paragraph = $order->get('field_sessions')->getValue();
			// 	foreach ( $paragraph as $element ) {
			// 	  $p = \Drupal\paragraphs\Entity\Paragraph::load( $element['target_id'] );
			// 	  $text = $p->field_dates->getValue();
			// 	  var_dump($text);die;
			// 	}
			// }
			$nids_order = \Drupal::entityQuery('node')
			    ->condition('status', 1)
			    ->condition('type', 'commande')/*CONTENT TYPE ? panier OR commande ?*/
			    // ->condition('field_statut_commande', 15)
			    ->condition('field_mes_produits.entity.field_kits.target_id', $nids_formations, 'IN')
			    ->condition('field_mes_produits.entity.field_dates.value', $now->format('Y-m-d\TH').':00:00', '>')
			    ->condition('field_mes_produits.entity.field_dates.value', $in_hour->format('Y-m-d\TH').':00:00', '<')
			    ->execute();

			foreach ($nids_order as $order_key => $order_id) {
				$order = Node::Load($order_id);
				$user = User::load($order->get('field_client')->target_id);
				$user_mail = $user->getEmail();
				$user_lastname = $user->get('field_nom')->value;
				$user_firstname = $user->get('field_prenom')->value;
				$user_phone = $user->get('field_telephone')->value;
				$user_city = $user->get('field_ville')->value;
				$user_zipcode = $user->get('field_code_postale')->value;
				$user_address = $user->get('field_adresse')->value;
				$user_address_sup = $user->get('field_complement_adresse')->value;

				$mailManager = \Drupal::service('plugin.manager.mail');
				$module = 'gz_ajax';
				$key = 'send_alert_hourly';
				$to = $user_mail;

				$params['message'] = 'Votre formation est dans 1 heure !';

				$params['headers'] = [
					'Content-Type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
					'Reply-To' => $user_mail,
					'Bcc' => $user_mail,
				];

				$langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
				$send = true;
				$result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
			}
		}

		return new JsonResponse($result);
	}
}