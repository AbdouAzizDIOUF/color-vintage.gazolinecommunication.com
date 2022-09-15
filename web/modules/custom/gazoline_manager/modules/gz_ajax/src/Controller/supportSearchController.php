<?php

namespace Drupal\gz_ajax\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

class supportSearchController extends ControllerBase
{
	public function content() {
		$models = array();

		$search = $_POST['search'];
		$current_langcode =  \Drupal::languageManager()->getCurrentLanguage()->getId();
		$connection = \Drupal::database();
		$query = $connection->select('node_field_data', 'nfd');
		$query->leftJoin('node__field_modeles', 'nfm', 'nfd.nid = nfm.field_modeles_target_id');
		$query->leftJoin('node__body', 'nb', 'nfd.nid = nb.entity_id');
		$query->leftJoin('node__field_image', 'nfi', 'nfm.field_modeles_target_id = nfi.entity_id');
		$query->leftJoin('node__field_categorie', 'nfc', 'nfm.entity_id = nfc.entity_id');
		$query->leftJoin('taxonomy_term_field_data', 'ttfd', 'nfc.field_categorie_target_id = ttfd.tid');
		$query->leftJoin('node_field_data', 'nfd2', 'nfm.entity_id = nfd2.nid');
		$query->leftJoin('node__body', 'nb2', 'nfd2.nid = nb2.entity_id');
		$query->fields('nfd', ['nid', 'title']);
		$query->fields('nfi', ['field_image_target_id', 'field_image_alt']);
		$query->fields('ttfd', ['name', 'tid']);
//		$query->condition('nb2.langcode', $current_langcode);
		$query->condition('nfm.langcode', $current_langcode);
		$query->condition('nfd.langcode', $current_langcode);
		$query->condition('nfi.langcode', $current_langcode);
		$query->condition('nfc.langcode', $current_langcode);
		$query->condition('nfd2.langcode', $current_langcode);
		$query->condition('ttfd.langcode', $current_langcode);
		$query->condition('nb.langcode', $current_langcode);
		$query->condition('nfd.type', 'modele');
		$condition_or = $query->orConditionGroup();
		$condition_or->condition('nfd.title', '%'.$search.'%', 'LIKE');
		$condition_or->condition('ttfd.name', '%'.$search.'%', 'LIKE');
		$condition_or->condition('nfd2.title', '%'.$search.'%', 'LIKE');
		$condition_or->condition('ttfd.description__value', '%'.$search.'%', 'LIKE');
		$condition_or->condition('nb.body_value', '%'.$search.'%', 'LIKE');
		$condition_or->condition('nb2.body_value', '%'.$search.'%', 'LIKE');
		$query->condition($condition_or);
		$result_models = $query->execute()->fetchAll();

		foreach ($result_models as $key => $model) {
			$file = File::load($model->field_image_target_id);
			$path = $file->getFileUri();
			$parent_term = '';
			$this->loadParentTerms($model->tid, $parent_term);
			$alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$model->nid);
			$url_image = ImageStyle::load('145x165')->buildUrl($file->getFileUri());
			$models[] = [
				'title' => $model->title,
				'nid' => $model->nid,
				'image' => $path,
				'category' => $model->name,
				'parent_category' => $parent_term,
				'url' => $alias,
				'url_image' => $url_image,
			];
		}
		return new JsonResponse($models);
	}

	private function loadParentTerms($tid, &$parent_term) {
		$terms_parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($tid);
		if ($terms_parent) {
			foreach($terms_parent as $term) {
				$parent_term = $term->getName();
				$this->loadParentTerms($term->get('tid')->value, $parent_term);
			}
		}
	}

}