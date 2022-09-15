<?php
namespace Drupal\offres_module\Service;


use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

class OffresService{

    public function getOffres(): array{
        $offresArray = array();
        $offres= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'offre']);
        $i=0;
        foreach ($offres as $offre) {
            $path = File::load($offre->get('field_image_header')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $offre->id()], ['absolute' => TRUE]);
            $offresArray[] = array(
                "order" => ($i % 2) ? 'evenq' : 'odd',
                "id" => $offre->id(),
                'title' => $offre->getTitle(),
                'body' => $offre->get('body')->value,
                'image' => $url,
                'link' => $link->toString(),
            );
            $i++;
        }
        return $offresArray;
    }


    
}