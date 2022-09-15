<?php

namespace Drupal\kits_module\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;


class KitsController extends ControllerBase
{
    
    /**
     * Met à jour le nombre de kit dans le panier
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNumberOfKitToCartAction(Request $request)
    {
        $id = $request->request->all()['id'];
        $nombre = (int)$request->request->all()['nombre'];
        $kits = Drupal::entityTypeManager()->getStorage('node')->loadByProperties([/*'type' => 'nos_kits',*/ 'nid' => $id]);
        $panier = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => Drupal::currentUser()->id()]);
        if ($kits && $panier) {
            $panier = reset($panier);
            $mesKitsPanier = $panier->field_mes_kits->getValue();
            foreach ($mesKitsPanier as $key => $value) {
                $paragraph = Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
                $paragraph = reset($paragraph);
                if (!empty($paragraph) && $id === $paragraph->field_kits->target_id) {
                    $sommeInitiale = (int)$panier->field_somme_totale->value - ((int)reset($kits)->field_prix->value * (int)$paragraph->field_nombre->value);
                    $paragraph->set('field_nombre', $nombre);
                    $paragraph->save();
                    $updateSommeTotale = (int)$sommeInitiale + ((int)reset($kits)->field_prix->value * (int)$nombre);
                    $panier->set('field_somme_totale', ($updateSommeTotale<0)?0:$updateSommeTotale);
                    $panier->save();
                }
            }
        }

        $kitsPanier = Drupal::service('kits_module.service')->getKitsPanier();

        return new JsonResponse([
            'id' => $id, 'nombre' => $nombre, 'kits'=> $kitsPanier['kits'],
            'sommes' => $kitsPanier['sommes'],
            'sommesKits' => $kitsPanier['sommesKits'], 'sommesFormations' => $kitsPanier['sommesFormations']
        ]);
    }


    /**
     * Suppression d'un kit du panier
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteKitAction(Request $request)
    {
        //dump($_GET);die;
        $kits = Drupal::entityTypeManager()->getStorage('node')->loadByProperties([/*'type' => 'nos_kits',*/ 'nid' => $_POST['id']]);
        $panier = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => Drupal::currentUser()->id()]);
        if (!empty($kits) && !empty($panier)) {
            $panier = reset($panier);
            $mesKitsPanier = $panier->field_mes_kits->getValue();
            $mesKitsPanierTmp = [];
            foreach ($mesKitsPanier as $key => $value) {
                $paragraph = Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
                $paragraph = reset($paragraph);
                if (!empty($paragraph)) {
                    if ($request->get('id') === $paragraph->field_kits->target_id) {
                        $nombre = $paragraph->field_nombre->value;
                        $paragraph->delete();
                    }else{
                        $mesKitsPanierTmp[] = $value;
                    }
                }
            }
            $mesKitsPanier = $mesKitsPanierTmp;
            $panier->set('field_mes_kits', $mesKitsPanier);
            $updateSommeTotale = (int)$panier->field_somme_totale->value - ((int)reset($kits)->field_prix->value * (int)$nombre);
            $panier->set('field_somme_totale', ($updateSommeTotale<0)?0:$updateSommeTotale);
            $panier->save();
        }


        return new JsonResponse(
            [
                'status' => 'success',
                /*'kits'=> $kitsPanier['kits'],
                'nbkitsPanier'=> count($kitsPanier['kits']),
                'somme' => $kitsPanier['sommes'],
                'sommesKits' => $kitsPanier['sommesKits'],
                'sommesFormations' => $kitsPanier['sommesFormations']*/
            ]);
        /*$kitsPanier = Drupal::service('kits_module.service')->getKitsPanier(); //dump($kitsPanier);
        $infosUser = Drupal::service('kits_module.service')->getInfosUser();
        $clickcollecte = Drupal::service('kits_module.service')->getValueBlockClickCollecteCollissimo('clickcollecte');
        //dump($clickcollecte);
        $collissimo = Drupal::service('kits_module.service')->getValueBlockClickCollecteCollissimo('collissimo');

        return [
            '#theme' => 'panier',
            '#data' => ['kits'=>$kitsPanier['kits'] ?? NULL, 'infosLivraison'=>$infosUser, 'clickcollecte ' => $clickcollecte, 'collissimo' => $collissimo],
            '#sommesTotale' => $kitsPanier['sommes'] ?? NULL,
            '#sommes' => ['sommesKits' => $kitsPanier['sommesKits'], 'sommesFormations' => $kitsPanier['sommesFormations']]
        ];*/
    }


    /**
     * Search kits
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchKits(Request $request)
    {
        $kits = Drupal::service('kits_module.service')->getKits();
        $params = $request->request->all()['search'];
        $params = str_replace('&scales=', 'scales=', $params);
        $params = explode('scales=', $params);
        array_shift($params);
        $kitsResults = Drupal::service('kits_module.service')->getKits($params);
        if (\Drupal::currentUser()->isAuthenticated()) {
            $logged = true;
        }
        return new JsonResponse(['kits' => (empty(($params)) ? $kits : $kitsResults), "logged" => $logged]);
    }


    /**
     * Ajout Kit au panier
     *
     * @param Request $request
     * @return mixed
     */
    public function panierAction(Request $request)
    {
        //dump($request);
        $user = User::load(Drupal::currentUser()->id());
        $kitID = $request->query->get('add');
        $panier = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => Drupal::currentUser()->id()]);
        if ($kitID)
        {
            $kits = Drupal::entityTypeManager()->getStorage('node')->loadByProperties([/*'type' => 'nos_kits', */'nid' => $kitID]);
            if (!empty($kits)) {
                if (empty($panier))
                {
                    $paragraphKit = Paragraph::create(['type' => 'panier']);
                    $paragraphKit->set('field_kits', $kitID);
                    $paragraphKit->set('field_nombre', 1);
                    $paragraphKit->isNew();
                    $paragraphKit->save();

                    $current[] = array(
                        'target_id' => $paragraphKit->id(),
                        'target_revision_id' => $paragraphKit->getRevisionId(),
                    );

                    $panierCreated = Drupal::entityTypeManager()->getStorage('node')->create([
                        'type' => 'panier',
                        'title' => 'Panier du client : ' . $user->name->value,
                        'field_client' => Drupal::currentUser()->id(),
                        'field_mes_kits' => $current,
                        'field_somme_totale' => reset($kits)->field_prix->value,
                        'field_statut_commande' => 16,
                    ]);
                    $panierCreated->save();
                } else {
                    $panier = reset($panier);
                    $mesKitsPanier = $panier->field_mes_kits->getValue();
                    $kitsListsIDs = [];
                    $mesKitsPanierTmp = [];
                    foreach ($mesKitsPanier as $key => $value) {
                        $paragraph = Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
                        if ($paragraph) {
                            $kitsListsIDs[] = reset($paragraph)->field_kits->target_id;
                            $mesKitsPanierTmp[] = $value;
                        }
                    }

                    $mesKitsPanier= $mesKitsPanierTmp;
                    if (!in_array($kitID, $kitsListsIDs, true)) {
                        $kitsListsIDs[] = $kitID;
                        $paragraphKit = Paragraph::create(['type' => 'panier']);
                        $paragraphKit->set('field_kits', $kitID);
                        $paragraphKit->set('field_nombre', 1);
                        $paragraphKit->isNew();
                        $paragraphKit->save();

                        $mesKitsPanier[] = array(
                            'target_id' => $paragraphKit->id(),
                            'target_revision_id' => $paragraphKit->getRevisionId(),
                        );

                        $panier->set('field_mes_kits', $mesKitsPanier);
                        $updateSommeTotale = (int)reset($kits)->field_prix->value + (int)$panier->field_somme_totale->value;
                        $panier->set('field_somme_totale',  ($updateSommeTotale<0)?0:$updateSommeTotale);
                        $panier->save();
                    }
                }
            }


            //$kitsPanier = Drupal::service('kits_module.service')->getKitsPanier();
            $kitsPanier = Drupal::service('kits_module.service')->getKitsPanier();
           // dump(count($kitsPanier['kits']));
            return new JsonResponse(['nbKitsPanier' => count($kitsPanier['kits'])]);
        }

        //return new JsonResponse(['ok' => 'Panier mis à jour']);

        $kitsPanier = Drupal::service('kits_module.service')->getKitsPanier(); //dump($kitsPanier);
        $infosUser = Drupal::service('kits_module.service')->getInfosUser();
        $clickcollecte = Drupal::service('kits_module.service')->getValueBlockClickCollecteCollissimo('clickcollecte');
     //   dump($clickcollecte);
        $collissimo = Drupal::service('kits_module.service')->getValueBlockClickCollecteCollissimo('collissimo');

       // dump($clickcollecte);
        // dump($kitsPanier);


        return [
            '#theme' => 'panier',
            '#data' => ['kits'=>$kitsPanier['kits'] ?? NULL, 'infosLivraison'=>$infosUser, 'clickcollecte' => $clickcollecte, 'collissimo' => $collissimo],
            '#sommesTotale' => $kitsPanier['sommes'] ?? NULL,
            '#sommes' => ['sommesKits' => $kitsPanier['sommesKits'], 'sommesFormations' => $kitsPanier['sommesFormations']]
        ];
    }

    public function contacterNous(){
        //dump($_POST);

        $message = "
            <div>
            <p><strong>Nom : </strong>".$_POST['name']." ".$_POST['lastname']."</p>
            <p><strong>Prenom : </strong>".$_POST['lastname']."</p>
            <p><strong>Email : </strong>".$_POST['email']."</p>
            <p><strong>Téléphone:". $_POST['phone']."</strong> </p><br>
            <div><p>Message : </div>".$_POST['Message']."</p><br>
        </div>
        ";
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "diouf@studiogazoline.com";
        $to = "diouf@studiogazoline.com";
        $subject = $_POST['Objet'];
        $header = "From: $from \r\n";
        $header .= "Cc:diouf@studiogazoline.com \r\n";
        /* $header .= "Cc:cg@lundimatin.fr \r\n";*/
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        $result = mail($to,$subject,$message, $header);
        return (new RedirectResponse('/confirmation-contact'))->send();
        ///confirmation-ccontacte
    }
}