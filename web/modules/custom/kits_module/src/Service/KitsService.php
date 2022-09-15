<?php
namespace Drupal\kits_module\Service;

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;


class KitsService{

    /*function __construct()
    {
        if (!\Drupal::currentUser()->isAuthenticated()) {
            $redirect = new RedirectResponse('/user-login');
            $redirect->send();
        }
    }*/

    public function getValueBlockClickCollecteCollissimo($typeBlock){
        $block = \Drupal\block\Entity\Block::load($typeBlock);
        $uuid = $block->getPluginId();
        $uuid = substr_replace($uuid, '', 0, 14);
        //dump($uuid);
        $block_content = \Drupal::service('entity.repository')->loadEntityByUuid('block_content', $uuid);
        if ($block_content) {
            $block_values = [
                'title' => $block_content->field_titre->value,
                'description' => $block_content->body->value,
            ];
        }

        return $block_values;
    }



    public function getInfosUser(){
        $user = User::load(\Drupal::currentUser()->id());
        $info_livraison = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'livraison', 'field_user_id' => \Drupal::currentUser()->id()]);
        $info_livraison = reset($info_livraison);

        if ($info_livraison){
            $info = [
                'id' => $user->get('uid')->getValue()[0]['value'] ?? null,
                'nom' => $info_livraison->get('field_nom')->getValue()[0]['value'] ?? null,
                'prenom' => $info_livraison->get('field_prenom')->getValue()[0]['value'] ?? null,
                'email' => $info_livraison->get('field_email')->getValue()[0]['value'] ?? null,
                'telephone' => $info_livraison->get('field_telephone')->getValue()[0]['value'] ?? null,
                'adresse' => $info_livraison->get('field_adresse')->getValue()[0]['value'] ?? null,
                'complementAdresse' => $info_livraison->get('field_complement_adresse')->getValue()[0]['value'] ?? null,
                'codePostal' => $info_livraison->get('field_cod')->getValue()[0]['value'] ?? null,
                'ville' => $info_livraison->get('field_ville')->getValue()[0]['value'] ?? null,
            ];
        }else{
            $info = [
                'id' => $user->get('uid')->getValue()[0]['value'],
                'nom' => $user->get('field_nom')->getValue()[0]['value'],
                'prenom' => $user->get('field_prenom')->getValue()[0]['value'],
                'email' => $user->get('mail')->getValue()[0]['value'],
            ];
        }

        //dump($info);
        return $info;
    }
    /**
     * Liste les kits d'un panier
     *
     * @return array
     */
    public function getKitsPanier(){
        $panier = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => \Drupal::currentUser()->id()]) ?? null;
        if ($panier) {
            $kits = [];
            $panier = reset($panier);
            $mesKitsPanier = $panier->field_mes_kits->getValue();
            $kitsListsIDs = [];
            $sommes = 0;
            $sommesKits = 0;
            $sommesFormations = 0;
            foreach ($mesKitsPanier as $key => $value)
            {
                $paragraph = \Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
                $paragraph = reset($paragraph);
                $kitsListsIDs[] = ['id' => $paragraph->field_kits->target_id, 'nombre' => $paragraph->field_nombre->value];
                $kit = $this->getKitByID($paragraph->field_kits->target_id);
                $kitNode = Node::load($kit['id']);
                if ($kitNode->getType() !== 'formations'){
                    $sommesKits += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                }else{
                    $sommesFormations += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                }
                $sommes += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                $kit['nombre'] = $paragraph->field_nombre->value;
                $kit['prixTotal'] = (int)$kit['prix'] * (int)$kit['nombre'];
                $kits[] = $kit;
            }
        }

        return array(
            'kits' => $kits ?? [],
            'sommes' => $sommes,
            'sommesKits' => $sommesKits ?? 0,
            'sommesFormations' => $sommesFormations
        );
    }

    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @param null $params
     * @return array
     */
    public function getKits($params=null): array{
        $kitsArray = array();
        $kits= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'nos_kits']);
        foreach ($kits as $kit) {
            $path = File::load($kit->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            //$url = ImageStyle::load('thumbnail')->buildUrl($path);
            $categorie = Term::load($kit->get('field_categorie')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
            if ($params){
                foreach ($params as $key => $value) {
                    if ($value !== '')
                    {
                        if ($value === $categorie->id()) {
                            $kitsArray[] = array(
                                "id" => $kit->id(),
                                'title' => $kit->getTitle(),
                                'body' => $kit->get('body')->value,
                                'prix' => $kit->get('field_prix')->value,
                                'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                                'image' => $url,
                                'link' => $link->toString(),
                            );
                        }
                    }
                }
            }else{
                $kitsArray[] = array(
                    "id" => $kit->id(),
                    'title' => $kit->getTitle(),
                    'body' => $kit->get('body')->value,
                    'prix' => $kit->get('field_prix')->value,
                    'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                    'image' => $url,
                    'link' => $link->toString(),
                );
            }
        }

        return $kitsArray;
    }



    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @param null $params
     * @return array
     */
    public function getKitsByCatégorie($categorie): array{
        $kitsArray = array();
        $kits= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'nos_kits', 'field_categorie' => $categorie]);
        foreach ($kits as $kit) {
            $path = File::load($kit->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $categorie = Term::load($kit->get('field_categorie')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
            $kitsArray[] = array(
                "id" => $kit->id(),
                'title' => $kit->getTitle(),
                'body' => $kit->get('body')->value,
                'prix' => $kit->get('field_prix')->value,
                'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                'image' => $url,
                'link' => $link->toString(),
            );
        }

        return $kitsArray;
    }


    private function tags($tagIds=array()){
        $tags = [];
        foreach ($tagIds as $tagId){
            $tags[] = $tagId['target_id'];
        }

        return $tags;
    }

    private function checkIfExistKeyToArray($tab1=[], $tab2=[]){
        $existe = false;
        foreach ($tab1 as $tab){
            if (in_array($tab, $tab2, true)) {
                $existe = true;
            }
        }

        return $existe;
    }

    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @param null $params
     * @return array
     */
    public function getKitsByTags($tags): array{
        $kitsArray = array();
        $kits= \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'nos_kits']);
        $tagsFormation = $this->tags($tags);
        foreach ($kits as $kit){
            $tagsKit = $kit->field_tags->getValue();
            if (!empty($tagsKit)){
                $tagsKitTabs = $this->tags($tagsKit);
                if ($this->checkIfExistKeyToArray($tagsFormation, $tagsKitTabs)){
                    $path = File::load($kit->get('field_image')->target_id)->getFileUri();
                    $url = str_replace('public://', '/sites/default/files/', $path);
                    $categorie = Term::load($kit->get('field_categorie')->target_id);
                    $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
                    $kitsArray[] = array(
                        "id" => $kit->id(),
                        'title' => $kit->getTitle(),
                        'body' => $kit->get('body')->value,
                        'prix' => $kit->get('field_prix')->value,
                        'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                        'image' => $url,
                        'link' => $link->toString(),
                    );
                }
            }
        }
        
        return $kitsArray;
    }



    /**
     * Permet d'obtenir un kit selon son ID
     *
     * @param $id
     * @return array
     */
    public function getKitByID($id): array{
        $kitsArray = array();
        $kits = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([/*'type' => 'nos_kits',*/ 'nid' => $id]);
        $kit = reset($kits);
        if (isset($kit->get('field_image')->target_id)) {
            $path = File::load($kit->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
        }

        $categorie = Term::load($kit->get('field_categorie')->target_id);
        $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
        if ($kit->getType() === 'formations'){
            $kitsArray = array(
                "id" => $kit->id(),
                'title' => $kit->getTitle(),
                'body' => $kit->get('body')->value,
                'prix' => $kit->get('field_prix')->value ?? 0,
                'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                'image' => $url,
                'link' => $link->toString(),
                'formation' => 'formation',
            );
        }else{
            $kitsArray = array(
                "id" => $kit->id(),
                'title' => $kit->getTitle(),
                'body' => $kit->get('body')->value,
                'prix' => $kit->get('field_prix')->value ?? 0,
                'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                'image' => $url,
                'link' => $link->toString(),
                'formation' => null,
            );
        }

        return $kitsArray ?? [];
    }

    /**
     * Obtenir tous les categories de kits
     *
     * @return array
     */
    public function getCategoriesKits(): array{
        $categoriesKitsArray = array();
        $categoriesKits= \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'nos_formations']);
        foreach ($categoriesKits as $categorie) {
            $categoriesKitsArray[] = array(
                "id" => $categorie->id(),
                'name' => $categorie->getName(),
            );
        }

        return $categoriesKitsArray;
    }


    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @return array
     */
    public function getTemoignages(): array{
        $temoignagesArray = array();
        $temoignages = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'temoignage']);
        //dump($temoignages);
        foreach ($temoignages as $temoignage){
            //$categorie = Term::load($formation->get('field_categorie')->target_id);
            $path = File::load($temoignage->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $temoignage->id()], ['absolute' => TRUE]);
            $temoignagesArray[] = array(
                "id" => $temoignage->id(),
                /*'title' => $kit->getTitle(),*/
                'nom' => $temoignage->get('field_nom')->value,
                'prenom' => $temoignage->get('field_prenom')->value,
                'body' => $temoignage->get('body')->value,
                'image' => $url,
                'link' => $link->toString(),
            );
        }

        return $temoignagesArray;
    }


    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @return array
     */
    public function getOnTemoigne(): array{
        $temoignagesArray = array();
        $temoignages = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'temoignage', 'field_a_temoigne' => 1]);
        //dump($temoignages);
        foreach ($temoignages as $temoignage){
            $path = File::load($temoignage->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $temoignage->id()], ['absolute' => TRUE]);
            $temoignagesArray[] = array(
                "id" => $temoignage->id(),
                /*'title' => $kit->getTitle(),*/
                'nom' => $temoignage->get('field_nom')->value,
                'prenom' => $temoignage->get('field_prenom')->value,
                'body' => $temoignage->get('body')->value,
                'image' => $url,
                'link' => $link->toString(),
            );
        }

        return $temoignagesArray;
    }


    /**
     * Permet d'obtenir tous les kits ou seulement un selon la caterogorie de kit
     *
     * @return array
     */
    public function getFiches(): array{
        $fichesArray = array();
        $fiches = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'fiche']);
        foreach ($fiches as $fiche){
            $path = File::load($fiche->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $fiche->id()], ['absolute' => TRUE]);

            $icone_url = $fiche->field_icone->entity->getFileUri();
            $urlIcone = str_replace('public://', '/sites/default/files/', $icone_url);

            $fichesArray[] = array(
                "id" => $fiche->id(),
                'title' => $fiche->getTitle(),
                'body' => $fiche->get('body')->value,
                'image' => $url,
                'icone' => $urlIcone,
                'link' => $link->toString(),
            );
        }

        return $fichesArray;
    }

}