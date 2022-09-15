<?php
namespace Drupal\articles_module\Service;


use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

class ArticlesService{

    public function getArticles(): array{
        $articlesArray = array();
        $query = \Drupal::entityQuery('node')
            ->condition('type', 'article')
            ->condition('status', 1)
            ->sort('created', 'DESC');
        $nids = $query->execute();

        $articles = Node::loadMultiple($nids); //dump($articles);
        foreach ($articles as $key => $article) {
            $path = File::load($article->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            //$categorie = Term::load($article->get('field_tags')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $key], ['absolute' => TRUE]);
            $articlesArray[] = array(
                "id" => $key,
                'title' => $article->getTitle(),
                'body' => $article->get('body')->value,
                //'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                'image' => $url,
                'link' => $link->toString(),
            );
        }

        return $articlesArray;
    }

}