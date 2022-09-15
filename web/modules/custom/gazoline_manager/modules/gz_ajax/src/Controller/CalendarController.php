<?php

namespace Drupal\gz_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\paragraphs\Entity\Paragraph;
use \Drupal\node\Entity\Node;
use mikehaertl\wkhtmlto\Pdf;

class CalendarController extends ControllerBase
{
	public function getActivites() {
		$response = array();

		$query = \Drupal::entityQuery('node'); 
		$query->condition('status', 1);
		$query->condition('type', 'formations');
		$cours = $query->execute();
		$count = 0;
		foreach ($cours as $key => $cour) {
			$cour_loaded = Node::Load($cour);
			$schedule = $cour_loaded->get('field_sessions')->getValue();
			$url = $cour_loaded->toUrl()->toString();
			foreach ($schedule as $class ) {
				$p = Paragraph::load($class['target_id'] );
				if ($p->get('field_dates')->value) {
					$count++;
					$startDate = \DateTime::createFromFormat('Y-m-d\TH:i:s', $p->get('field_dates')->value, new \DateTimeZone('UTC'));
					$startDate->setTimezone(new \DateTimeZone('Europe/Paris'));
					$endDate = \DateTime::createFromFormat('Y-m-d\TH:i:s', $p->get('field_date_fin')->value, new \DateTimeZone('UTC'));
					$endDate->setTimezone(new \DateTimeZone('Europe/Paris'));
					$response[] = array(
						'id' => $count,
						'calendarId' => '1',
						'category' => 'time',
						'title' => $cour_loaded->get('title')->value,
						'start' => $startDate->format('Y-m-d\TH:i:s'),
						'end' => $endDate->format('Y-m-d\TH:i:s'),
						'body' => '<a href="'.$url.'">Voir la formation</a>',
						'isReadOnly' => TRUE,
					);
				}
			}
		}

		return new JsonResponse($response);
	}
}