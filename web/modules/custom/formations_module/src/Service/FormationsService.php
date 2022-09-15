<?php
namespace Drupal\formations_module\Service;


use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

class FormationsService{

    private function checkIfExistKeyToArray($tab1=[], $tab2=[]){
        $existe = false;
        foreach ($tab1 as $tab){
            if (in_array($tab, $tab2, true)) {
                $existe = true;
            }
        }

        return $existe;
    }

    private function tags($tagIds=array()){
        $tags = [];
        foreach ($tagIds as $tagId){
            $tags[] = $tagId['target_id'];
        }

        return $tags;
    }


    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @param null $params
     * @return array
     */
    public function getArticlesByTags($tags): array{
        $kitsArray = array();
        $kits= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'article']);
        $tagsFormation = $this->tags($tags);
        foreach ($kits as $kit){
           // dump($kit);
            $tagsKit = $kit->field_tags->getValue();
            if (!empty($tagsKit)){
                $tagsKitTabs = $this->tags($tagsKit);
                if ($this->checkIfExistKeyToArray($tagsFormation, $tagsKitTabs)){
                    $path = File::load($kit->get('field_image')->target_id)->getFileUri();
                    $url = str_replace('public://', '/sites/default/files/', $path);
                    //$categorie = Term::load($kit->get('field_categorie')->target_id);
                    $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
                    $kitsArray[] = array(
                        "id" => $kit->id(),
                        'title' => $kit->getTitle(),
                        'body' => $kit->get('body')->value,
                        'image' => $url,
                        'link' => $link->toString(),
                    );
                }
            }
        }

        return $kitsArray;
    }
    public function getFormationsByTags($tagIds=array()){
        $tagsKit = $this->tags($tagIds);
        $formationsArray = array();
        $category = \Drupal::request()->query->get('category');
        if ($category){
            $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations', 'field_categorie' => $category]);
        }else{
            $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations']);
        }

        $i=1;
        foreach ($formations as $formation)
        {
           $tagsFormation = $this->tags($formation->field_tags->getValue());

            $existe = $this->checkIfExistKeyToArray($tagsKit,$tagsFormation);

            if( (is_null($formation->field_prochaine_formation->value) || ($formation->field_prochaine_formation->value === '0')) &&
                !empty($formation->field_tags->getValue()) && $existe)
            {
                // dump($formation->field_prochaine_formation->value);
                $path = File::load($formation->get('field_image')->target_id)->getFileUri();
                $url = str_replace('public://', '/sites/default/files/', $path);
                $categorie = Term::load($formation->get('field_categorie')->target_id);
                $link = Url::fromRoute('entity.node.canonical', ['node' => $formation->id()], ['absolute' => TRUE]);
                if ($i===3){
                    if (is_null($groups))
                    {
                        $formationsArray[] = array("order" => 'middle', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }else{
                        $formationsArray[$categorie->id()][] = array("order" => 'middle', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }
                }else{
                    if (is_null($groups)) {
                        $formationsArray[] = array("order" => ($i % 2) ? 'evenq' : 'odd', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }else{
                        $formationsArray[$categorie->id()][] = array("order" => ($i % 2) ? 'evenq' : 'odd', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }
                }
                $i++;
            }

        }

        return $formationsArray;
    }


    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @return array
     */
    public function getFormations($groups=null): array{
        $formationsArray = array();
        $category = \Drupal::request()->query->get('category');

        if ($category){
            $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations', 'field_categorie' => $category]);
        }else{
            $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations']);
        }

        $i=1;
        foreach ($formations as $formation)
        {
            if(is_null($formation->field_prochaine_formation->value) || ($formation->field_prochaine_formation->value === '0') )
            {
               // dump($formation->field_prochaine_formation->value);
                $path = File::load($formation->get('field_image')->target_id)->getFileUri();
                $url = str_replace('public://', '/sites/default/files/', $path);
                $categorie = Term::load($formation->get('field_categorie')->target_id) ?? NULL;
                $link = Url::fromRoute('entity.node.canonical', ['node' => $formation->id()], ['absolute' => TRUE]);
                if ($i===3){
                    if (is_null($groups))
                    {
                        $formationsArray[] = array("order" => 'middle', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }else{
                        if (!empty($categorie) && $categorie !== null){
                            $formationsArray[$categorie->id()][] = array("order" => 'middle', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());

                        }
                    }
                }else{
                    if (is_null($groups)) {
                        $formationsArray[] = array("order" => ($i % 2) ? 'evenq' : 'odd', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                    }else{
                        if (!empty($categorie) && $categorie !== null){
                            $formationsArray[$categorie->id()][] = array("order" => ($i % 2) ? 'evenq' : 'odd', "id" => $formation->id(), 'title' => $formation->getTitle(), 'body' => $formation->get('body')->value, 'duree' => $formation->get('field_duree')->value, 'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()], 'image' => $url, 'link' => $link->toString());
                        }
                    }
                }
                $i++;
            }

        }

        return $formationsArray;
    }



    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @return array
     */
    public function getProchaineFormationsHomePage(): array{
        $formationsArray = array();
        $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations', 'field_prochaine_formation' => true]);
        foreach ($formations as $formation) {
           $datedebut = '';
            if (!is_null($formation->field_sessions->getValue()['0']['target_id'])){
                $idParagraphSession = $formation->field_sessions->getValue()['0']['target_id'];
                $session = Paragraph::load($idParagraphSession);
                $datedebut = $session->field_dates->value;
                //$datedebut = date_create($datedebut);
               // $datedebut = date_format($datedebut,"d/m/Y");
               // dump($datedebut);

            }
            //$idParagraphSession = $formation->field_sessions->getValues();dump($idParagraphSession);
            //$session = Paragraph::load();
            /*$path = File::load($formation->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);*/
            $categorie = Term::load($formation->get('field_categorie')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $formation->id()], ['absolute' => TRUE]);
            //dump($categorie);die;
            if (!empty($categorie) && $categorie !== null){
                $formationsArray[$categorie->id()][] = array(
                    "id" => $formation->id(),
                    'title' => $formation->getTitle(),
                    'body' => $formation->get('body')->value,
                    'duree' => $formation->get('field_duree')->value,
                    'date_debut' => $datedebut,
                    'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                    /*'image' => $url,*/
                    'link' => $link->toString(),
                );
            }

        }
        return $formationsArray;
    }


    public function getFormationsHomePage(): array{
        $formationsArray = array();
        $formations= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'formations', 'field_home_page' => true]);
        $i=1;
        foreach ($formations as $formation) {
            $path = File::load($formation->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $categorie = Term::load($formation->get('field_categorie')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $formation->id()], ['absolute' => TRUE]);
            $formationsArray[] = array(
                "order" => ($i % 2) ? 'evenq' : 'odd',
                "id" => $formation->id(),
                'title' => $formation->getTitle(),
                'body' => $formation->get('body')->value,
                'duree' => $formation->get('field_duree')->value,
                'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                'image' => $url,
                'link' => $link->toString(),
            );
            $i++;
        }
        return $formationsArray;
    }


    public function getFormationByID($nid): array{
        $formationsArray = array();
        $formation=Node::load($nid);
        $path = File::load($formation->get('field_image')->target_id)->getFileUri();
        $url = str_replace('public://', '/sites/default/files/', $path);
        $categorie = Term::load($formation->get('field_categorie')->target_id);
        $link = Url::fromRoute('entity.node.canonical', ['node' => $formation->id()], ['absolute' => TRUE]);
        return array(
            "id" => $formation->id(),
            'title' => $formation->getTitle(),
            'link' => $link->toString(),
        );
    }

    public function getFormationsProgrammes($params)
    {
        $formations = [];
        foreach ($params as $id){
            $formations[] = $this->getFormationByID($id['target_id']);
        }

        return $formations;
    }
    /**
     * Obtenir tous les categories de kits
     *
     * @return array
     */
    public function getCategoriesFormations(): array{
        $categoriesKitsArray = array();
        $categoriesKits= \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'categorie_formation']);
        foreach ($categoriesKits as $categorie) {
            $categoriesKitsArray[] = array(
                "id" => $categorie->id(),
                'name' => $categorie->getName(),
            );
        }

        return $categoriesKitsArray;
    }
    
}