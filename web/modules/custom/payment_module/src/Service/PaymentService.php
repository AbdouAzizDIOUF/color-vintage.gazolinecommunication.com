<?php
namespace Drupal\payment_module\Service;


use Omnipay\Omnipay;
use Drupal;
use Drupal\user\Entity\User;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;


class PaymentService{

    public function __construct(){
        $clientId = "AZYflCS3pwwHCqFy4JVc4VmBRFxAKIMeibx8Izw9UMIxnQtqgsCeLAMUO4R-FPM-yR6NaLtySdFkW83Q";
        $clientSecret = "EPnDTPKVoO0H_hLsb2_cB1ISXRh-oMOYzTFwaDWoROaWENo1vQjlepLR5MSJbLGUByRcqb2AM74MTuEk";

        define('CLIENT_ID', $clientId);
        define('CLIENT_SECRET', $clientSecret);

        define('PAYPAL_RETURN_URL', 'https://color-vintage.gazolinecommunication.com/payment?paypal=true&success=true');
        define('PAYPAL_CANCEL_URL', 'https://color-vintage.gazolinecommunication.com/payment?paypal=true&cancel=true');
        define('PAYPAL_CURRENCY', 'EUR'); // set your currency here
    }

    public function payer(){
        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId(CLIENT_ID);
        $gateway->setSecret(CLIENT_SECRET);
        $gateway->setTestMode(true); //set it to 'false' when go live
        if (array_key_exists('paymentId', $_GET) && array_key_exists('PayerID', $_GET)) {
            $transaction = $gateway->completePurchase(array(
                'payer_id'             => $_GET['PayerID'],
                'transactionReference' => $_GET['paymentId'],
            ));
            $response = $transaction->send();

            if ($response->isSuccessful()) {
                $arr_body = $response->getData();
               // dump($arr_body);die;
                $this->addNewCommande($arr_body['id']);
                
                echo "Payment is successful. Your transaction id is: ". $payment_id;
            } else {
                echo $response->getMessage();
            }
        } else {
            try {
                $_SESSION['adresseLivraison'] = [
                    'name' => $_POST['name1'],
                    'lastname' => $_POST['lastname1'],
                    'email' => $_POST['email1'],
                    'adress' => $_POST['adress1'],
                    'adress_plus' => $_POST['adress_plus1'],
                    'codePostal' => $_POST['postal1'],
                    'ville' => $_POST['ville1'],
                    'phone' => $_POST['phone1'],
                    'nom_other' => $_POST['nom_other1'] ?? null,
                    'prenom_other' => $_POST['prenom_other1'] ?? null,
                    'societe_other' => $_POST['societe_other1'] ?? null,
                    'adress_other' => $_POST['adress_other1'] ?? null,
                    'adress_plus_other' => $_POST['adress_plus_other1'] ?? null,
                    'codePostal_other' => $_POST['postal_other1'] ?? null,
                    'ville_other' => $_POST['ville_other1'] ?? null,
                    'type_de_livraison' => $_POST['choixLivraisonValue1'] ?? null,
                    'amount' => $_POST['amount'],
                ];

                $response = $gateway->purchase(array(
                    'amount' => $_POST['amount'],
                    'currency' => PAYPAL_CURRENCY,
                    'returnUrl' => PAYPAL_RETURN_URL,
                    'cancelUrl' => PAYPAL_CANCEL_URL,
                ))->send();
               // dump($response);die;
                if ($response->isRedirect()) {
                    //return  $redirect->send();
                    $response->redirect(); // this will automatically forward the customer
                } else {
                    echo $response->getMessage();
                }
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }


    }


    public function payerStripe(){
        $gateway = Omnipay::create('Stripe');
        $gateway->setApiKey('sk_test_51KlvLlB9jYU0UI5MurdGogDnCU5v2Rjdc4HP9oQFowWXwdL0qBDun5NUsolkEy1D5GRp90gcQo6TA3cvZBVYlPX60044clJmia');
        $gateway->setTestMode(true); //set it to 'false' when go live

        if (isset($_POST['stripeToken']) && !empty($_POST['stripeToken']))
        {
            $_SESSION['adresseLivraison'] = [
                'name' => $_POST['name2'],
                'lastname' => $_POST['lastname2'],
                'email' => $_POST['email2'],
                'adress' => $_POST['adress2'],
                'adress_plus' => $_POST['adress_plus2'],
                'codePostal' => $_POST['postal2'],
                'ville' => $_POST['ville2'],
                'phone' => $_POST['phone2'],
                'nom_other' => $_POST['nom_other2'] ?? null,
                'prenom_other' => $_POST['prenom_other2'] ?? null,
                'societe_other' => $_POST['societe_other2'] ?? null,
                'adress_other' => $_POST['adress_other2'] ?? null,
                'adress_plus_other' => $_POST['adress_plus_other2'] ?? null,
                'codePostal_other' => $_POST['postal_other2'] ?? null,
                'ville_other' => $_POST['ville_other2'] ?? null,
                'type_de_livraison' => $_POST['choixLivraisonValue2'] ?? null,
                'amount' => $_POST['amount2'],
            ];
            try {
                $token = $_POST['stripeToken'];
                $response = $gateway->purchase([
                    'amount' => $_POST['amount2'],
                    'currency' => 'USD',
                    'token' => $token,
                ])->send();

                if ($response->isSuccessful()) {
                    // payment was successful: update database
                    $arr_payment_data = $response->getData();
                    $payment_id = $arr_payment_data['id'];
                    $amount = $_POST['amount2'];
                   // dump($arr_payment_data);die;
                    $this->addNewCommande($arr_payment_data['id'], 'stripe');

                    $redirect = new RedirectResponse('/confirmation-commande');
                    return  $redirect->send();
                    echo "Payment is successful. Your payment id is: ". $payment_id;
                } else {
                    // payment failed: display message to customer
                    echo $response->getMessage();
                }
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }else{
            try {

                $_SESSION['adresseLivraison'] = [
                    'name' => $_POST['name2'],
                    'lastname' => $_POST['lastname2'],
                    'email' => $_POST['email2'],
                    'adress' => $_POST['adress2'],
                    'adress_plus' => $_POST['adress_plus2'],
                    'codePostal' => $_POST['postal2'],
                    'ville' => $_POST['ville2'],
                    'phone' => $_POST['phone2'],
                    'nom_other' => $_POST['nom_other2'] ?? null,
                    'prenom_other' => $_POST['prenom_other2'] ?? null,
                    'societe_other' => $_POST['societe_other2'] ?? null,
                    'adress_other' => $_POST['adress_other2'] ?? null,
                    'adress_plus_other' => $_POST['adress_plus_other2'] ?? null,
                    'codePostal_other' => $_POST['postal_other2'] ?? null,
                    'ville_other' => $_POST['ville_other2'] ?? null,
                    'type_de_livraison' => $_POST['choixLivraisonValue2'] ?? null,
                    'amount' => $_POST['amount2'],
                ];

                $response = $gateway->purchase(array(
                    'amount' => $_POST['amount2'],
                    'currency' => PAYPAL_CURRENCY,
                    'returnUrl' => PAYPAL_RETURN_URL,
                    'cancelUrl' => PAYPAL_CANCEL_URL,
                ))->send();

                if ($response->isRedirect()) {
                    $redirect = new RedirectResponse('/confirmation-commande');
                    return  $redirect->send();
                    $response->redirect(); // this will automatically forward the customer
                } else {
                    echo $response->getMessage();
                }
            } catch(Exception $e) {
                echo $e->getMessage();
            }
        }
    }


    private function createLivraisonAdresse():?array
    {
        $paragraphLivraison = Paragraph::create([
            'type' => 'livraison',
            'field_client' => Drupal::currentUser()->id(),
            'field_nom' => $_SESSION['adresseLivraison']['name'],
            'field_prenom' => $_SESSION['adresseLivraison']['lastname'],
            'field_email' => $_SESSION['adresseLivraison']['email'],
            'field_adresse' => $_SESSION['adresseLivraison']['adress'],
            'field_complement_adresse' => $_SESSION['adresseLivraison']['adress_plus'],
            'field_code_postal' => $_SESSION['adresseLivraison']['codePostal'],
            'field_telephone' => $_SESSION['adresseLivraison']['phone'],
            'field_ville' => $_SESSION['adresseLivraison']['ville'],

            'field_nom_autre' => $_SESSION['adresseLivraison']['nom_other'],
            'field_prenom_autre' => $_SESSION['adresseLivraison']['prenom_other'],
            'field_nom_de_la_societe	' => $_SESSION['adresseLivraison']['societe_other'],

            'field_adresse_autre' => $_SESSION['adresseLivraison']['adress_other'],
            'field_complement_adresse_autre' => $_SESSION['adresseLivraison']['adress_plus_other'],
            'field_code_postal_autre' => $_SESSION['adresseLivraison']['codePostal_other'],
            'field_ville_autre' => $_SESSION['adresseLivraison']['ville_other'],

            'field_type_de_livraison' => $_SESSION['adresseLivraison']['type_de_livraison'],
        ]);
        $paragraphLivraison->isNew();
        $paragraphLivraison->save();

        return array(
            'target_id' => $paragraphLivraison->id(),
            'target_revision_id' => $paragraphLivraison->getRevisionId(),
        );
    }

    private function addNewCommande($payment_id, $methodePayement = 'paypal'){
        $user = User::load(Drupal::currentUser()->id());
        $panier = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'panier', 'field_client' => Drupal::currentUser()->id()]);
        $panier = reset($panier);
        $mesKitsPanier = $panier->field_mes_kits->getValue();
        $current = [];
        foreach ($mesKitsPanier as $key => $value) {
            $paragraph = Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
            $paragraph = reset($paragraph);
            $productId = $paragraph->field_kits->target_id;
            $numberOfProduct = $paragraph->field_nombre->value;
            $paragraphKit = Paragraph::create(['type' => 'panier']);
            $paragraphKit->set('field_kits', $productId);
            $paragraphKit->set('field_nombre', $numberOfProduct);
            $paragraphKit->isNew();
            $paragraphKit->save();

            $current[] = array(
                'target_id' => $paragraphKit->id(),
                'target_revision_id' => $paragraphKit->getRevisionId(),
            );
        }

        $numeroCommande = 'COM-'.Drupal::currentUser()->id().time();
        $livraisonCreated = Node::create([
            'type' => 'livraison',
            'title' => 'Adresse de Livraison de la Commande : ' . $numeroCommande,
            'field_user_id' => Drupal::currentUser()->id(),
            'field_nom' => $_SESSION['adresseLivraison']['name'],
            'field_prenom' => $_SESSION['adresseLivraison']['lastname'],
            'field_email' => $_SESSION['adresseLivraison']['email'],
            'field_adresse' => $_SESSION['adresseLivraison']['adress'],
            'field_complement_adresse' => $_SESSION['adresseLivraison']['adress_plus'],
            'field_cod' => $_SESSION['adresseLivraison']['codePostal'],
            'field_telephone' => $_SESSION['adresseLivraison']['phone'],
            'field_ville' => $_SESSION['adresseLivraison']['ville'],

            'field_nom_autre' => $_SESSION['adresseLivraison']['nom_other'],
            'field_prenom_autre' => $_SESSION['adresseLivraison']['prenom_other'],
            'field_nom_de_la_societe	' => $_SESSION['adresseLivraison']['societe_other'],

            'field_adresse_autre' => $_SESSION['adresseLivraison']['adress_other'],
            'field_complement_adresse_autre' => $_SESSION['adresseLivraison']['adress_plus_other'],
            'field_code_postal_autre' => $_SESSION['adresseLivraison']['codePostal_other'],
            'field_ville_autre' => $_SESSION['adresseLivraison']['ville_other'],

            'field_type_de_livraison' => $_SESSION['adresseLivraison']['type_de_livraison'],
        ]);

        $livraisonCreated->save();

        $commande = Node::create([
            'type' => 'commande',
            'field_numero_commande' => $numeroCommande,
            'title' => 'Commande N°: '.$numeroCommande.' du client : ' . $user->name->value,
            'field_client' => Drupal::currentUser()->id(),
            'field_livraison' => $livraisonCreated->nid->value,
            'field_livraison_coordonnee' => $this->createLivraisonAdresse(),
            'field_somme_totale' =>$_SESSION['adresseLivraison']['amount'],//
            'field_mes_produits' => $current,
            'field_numero_paiement' => $payment_id,
            'field_methode_de_paiement' => $methodePayement,
        ]);

        $commande->save();
        $panier->delete();
        $this->envoieEmail($numeroCommande, $commande->nid->value, $livraisonCreated);

        return (new RedirectResponse('/confirmation-commande'))->send();
    }


    /**
     * Permet d'obtenir un kit selon son ID
     *
     * @param $id
     * @return array
     */
    public function getArticleCommandeByID($id, $numCommande, $typeProduct): array{
        $kitsArray = array();
        $kits = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['nid' => $id]);
        $kit = reset($kits);
        if ($kits == true){
            $path = File::load($kit->get('field_image')->target_id)->getFileUri();
            $url = str_replace('public://', '/sites/default/files/', $path);
            $categorie = Term::load($kit->get('field_categorie')->target_id);
            $link = Url::fromRoute('entity.node.canonical', ['node' => $kit->id()], ['absolute' => TRUE]);
            if ($kit->getType() === 'formations'){
                //$session = $this->getSessions($kit);
                $kitsArray = array(
                    "id" => $kit->id(),
                    'title' => $kit->getTitle(),
                    'body' => $kit->get('body')->value,
                    'prix' => $kit->get('field_prix')->value ?? 0,
                    'categories' => ['id' => $categorie->id(), 'name' => $categorie->getName()],
                    'image' => $url,
                    'link' => $link->toString(),
                    'formation' => 'formation',
                    'sessions' => $this->getSessions($kit),
                    'formateurs' => $this->getFormateur($kit),
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
        }


        return $kitsArray;
    }


    private function getFormateur($formation){
        if ($formation->field_formateur->target_id){
            $user = User::load($formation->field_formateur->target_id);
            return [
                'prenom' => $user->get('field_prenom')->value,
                'nom'=> $user->get('field_nom')->value
            ];
        }
        return null;
    }

    /**
     * @param $formation
     * @param int $nbPDF
     * @param int $nbVideos
     * @param int $nbLiensZoom
     * @return array
     */
    private function getSessions($formation)
    {
       // dump($nbPDF);
        if ($formation->field_sessions){
            $sessions = $formation->field_sessions->getValue();
            $sessionsTab = [];
            foreach ($sessions as $key => $value)
            {
                $paragraphs = \Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'session', 'id' => $value['target_id']]);
                foreach ($paragraphs as $paragraph)
                {
                    if ($paragraph->field_document){
                        $docsBySessionId = $paragraph->field_document->getValue();
                       // $countPDF += count($docsBySessionId);
                        //dump($countPDF);
                        $docs = [];
                        foreach ($docsBySessionId as $key2 => $value2){
                            $file = File::load($value2['target_id']);
                            $filename = str_replace('.pdf', '', $file->getFilename());
                            $filename = str_replace('_', ' ', $filename);
                            $document = str_replace('public://', '/sites/default/files/', $file->getFileUri());
                            $docs[] = [
                                'name' => $filename,
                                'url' => $document
                            ];
                        }
                    }


                    $sessionsTab[] = [
                        'titre' => $paragraph->get('field_titre')->value,
                        'zoom' => $paragraph->field_lien_zoom->value,
                        'video' => $paragraph->field_video->value,
                        'docs' => $docs,
                    ];
                }
            }
        }

        return $sessionsTab;
    }


    private function getNumberElementByCommande($kit){
        $nbPDF = 0;
        $nbZoom = 0;
        $nbVideo = 0;
        foreach ($commande['produits']['formations'] as $formation){

        }

    }



    private function detailCommandeByNumero($numeroCommande){
        $commande = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'commande']);
        $commande = reset($commande);
        $idLivraison = $commande->field_livraison_coordonnee->getValue()['0']['target_id'];
        $value = Paragraph::load($idLivraison);
        if ($value->field_nom_autre->value){
            $livraison =  [
                'nom' => $value->field_nom_autre->value,
                'prenom' => $value->field_prenom_autre->value,
                'ville' => $value->field_ville_autre->value,
                'Code' => $value->field_code_postal_autre->value,
                'adress_plus' => $value->field_complement_adresse_autre->value,
                'adresse' => $value->field_adresse_autre->value,
            ];
        }

        return $livraison ?? null;
    }
    /**
     * Liste les articles d'une commande
     *
     * @return array
     */
    public function getArticlesCommande(): array{
        $commandes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'commande', 'field_client' => \Drupal::currentUser()->id()]);
        $tt = array();
        $commandesList = array();
        $listOfFormations = array();
        foreach ($commandes as $commande)
        {
            $kitsByCommande = [];
            $mesProduitsCommande = $commande->field_mes_produits->getValue();
            $kitsListsIDs = [];
            $sommes = 5;
            $prodKits = [];
            $prods = [];
            foreach ($mesProduitsCommande as $key => $value)
            {
                $paragraph = \Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
                //dump($paragraph);die;
                $paragraph = reset($paragraph);
                $kitsListsIDs[] = ['id' => $paragraph->field_kits->target_id, 'nombre' => $paragraph->field_nombre->value];
                $products = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([/*'type' => 'nos_kits',*/ 'nid' => $paragraph->field_kits->target_id]);
                //dump($kits);die;
                $prod = reset($products);
                if ($prod && $prod->getType() === 'formations'){
                    $kit = $this->getArticleCommandeByID($paragraph->field_kits->target_id, $commande->field_numero_commande->value, 'formation');
                    $sommes += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                    $kit['nombre'] = $paragraph->field_nombre->value;
                    $kit['prixTotal'] = (int)$kit['prix'] * (int)$kit['nombre'];
                    $prods['formations'][] = $kit;
                    $listOfFormations[] = $kit;
                }else{
                    $kit = $this->getArticleCommandeByID($paragraph->field_kits->target_id, $commande->field_numero_commande->value, 'kit');
                    $sommes += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                    $kit['nombre'] = $paragraph->field_nombre->value;
                    $kit['prixTotal'] = (int)$kit['prix'] * (int)$kit['nombre'];
                    $prods['kits'][] = $kit;
                }
            }

            $commandesList[] = [
                'id' => $commande->id(),
                'date' => date("d.m.Y", $commande->get('created')->value),
                'prixTotal' => $commande->get('field_somme_totale')->value,
                'numCommande' => $commande->get('field_numero_commande')->value,
                'link' => Url::fromRoute('entity.node.canonical', ['node' => $commande->id()], ['absolute' => TRUE])->toString(),
                'produits' => $prods,
            ];

        }

        $listOfFormationsTmp = [];
        foreach ($listOfFormations as $formation)
        {
            $nbVideos = 0;
            $nbPDF = 0;
            $nbZoom = 0;
            foreach ($formation['sessions'] as $session){
                if($session['docs']){
                    $nbPDF = (int)$nbPDF + count($session['docs']);
                }
                if ($session['zoom']){
                    $nbZoom = (int)$nbZoom + 1;
                }
                if ($session['video']){
                    $nbVideos = (int)$nbVideo + 1;
                }
            }

            $formation['nbVideos'] = $nbVideos;
            $formation['nbPDF'] = $nbPDF;
            $formation['nbZoom'] = $nbZoom;
            $listOfFormationsTmp[] = $formation;
        }

        return array(
            'commandes' => $commandesList,
            'formations' => $listOfFormationsTmp,
            'sommes' => $sommes,
        );
    }

    public function getArticlesByNumeroCommande($numeroCommande, $commandeID=''): array{
        $commande = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'commande', 'field_client' => \Drupal::currentUser()->id(), 'field_numero_commande'=>$numeroCommande]);
        $commande = reset($commande);
        $kitsByCommande = [];
        $mesProduitsCommande = $commande->field_mes_produits->getValue();
        $kitsListsIDs = [];
        $sommes = 5;
        $prodKits = [];
        $prods = [];
        foreach ($mesProduitsCommande as $key => $value) {
            $paragraph = \Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => 'panier', 'id' => $value['target_id']]);
            $paragraph = reset($paragraph);
            $kitsListsIDs[] = ['id' => $paragraph->field_kits->target_id, 'nombre' => $paragraph->field_nombre->value];
            $products = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['nid' => $paragraph->field_kits->target_id]);
            //dump($kits);die;
            $prod = reset($products);
            if ($prod && $prod->getType() === 'formations') {
                $kit = $this->getArticleCommandeByID($paragraph->field_kits->target_id, $commande->field_numero_commande->value, 'formation');
                $sommes += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                $kit['nombre'] = $paragraph->field_nombre->value;
                $kit['prixTotal'] = (int)$kit['prix'] * (int)$kit['nombre'];
                $prods[] = $kit;
            } else {
                $kit = $this->getArticleCommandeByID($paragraph->field_kits->target_id, $commande->field_numero_commande->value, 'kit');
                $sommes += ((int)$kit['prix'] * (int)$paragraph->field_nombre->value);
                $kit['nombre'] = $paragraph->field_nombre->value;
                $kit['prixTotal'] = (int)$kit['prix'] * (int)$kit['nombre'];
                $prods[] = $kit;
            }
        }

            return $prods;
    }

    public function envoieEmail($numeroCommande, $commandeID=106, $liv=''){
        $livraison = $this->detailCommandeByNumero($numeroCommande);
        $articles = $this->getArticlesByNumeroCommande($numeroCommande);
        $message ="
        <!DOCTYPE html>
<html lang='en' xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
<head>
    <meta charset='utf-8'> <!-- utf-8 works for most cases -->
    <meta name='viewport' content='width=device-width'> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv='X-UA-Compatible' content='IE=edge'> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name='x-apple-disable-message-reformatting'>  <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <link href='https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700' rel='stylesheet'>

    <!-- CSS Reset : BEGIN -->
    <style>

        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*='margin: 16px 0'] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
        a {
            text-decoration: none;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],  /* iOS */
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        /* What it does: Prevents Gmail from changing the text color in conversation threads. */
        .im {
            color: inherit !important;
        }

        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img + div {
            display: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */

        /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u ~ div .email-container {
                min-width: 320px !important;
            }
        }
        /* iPhone 6, 6S, 7, 8, and X */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u ~ div .email-container {
                min-width: 375px !important;
            }
        }
        /* iPhone 6+, 7+, and 8+ */
        @media only screen and (min-device-width: 414px) {
            u ~ div .email-container {
                min-width: 414px !important;
            }
        }
    </style>

    <!-- CSS Reset : END -->

    <!-- Progressive Enhancements : BEGIN -->
    <style>

        .primary{
            background: #17bebb;
        }
        .bg_white{
            background: #ffffff;
        }
        .bg_light{
            background: #f7fafa;
        }
        .bg_black{
            background: #000000;
        }
        .bg_dark{
            background: rgba(0,0,0,.8);
        }
        .email-section{
            padding:2.5em;
        }

        /*BUTTON*/
        .btn{
            padding: 10px 15px;
            display: inline-block;
        }
        .btn.btn-primary{
            border-radius: 5px;
            background: #17bebb;
            color: #ffffff;
        }
        .btn.btn-white{
            border-radius: 5px;
            background: #ffffff;
            color: #000000;
        }
        .btn.btn-white-outline{
            border-radius: 5px;
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
        }
        .btn.btn-black-outline{
            border-radius: 0px;
            background: transparent;
            border: 2px solid #000;
            color: #000;
            font-weight: 700;
        }
        .btn-custom{
            color: rgba(0,0,0,.3);
            text-decoration: underline;
        }

        h1,h2,h3,h4,h5,h6{
            font-family: 'Work Sans', sans-serif;
            color: #000000;
            margin-top: 0;
            font-weight: 400;
        }

        body{
            font-family: 'Work Sans', sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0,0,0,.4);
        }

        a{
            color: #17bebb;
        }

        table{
        }
        /*LOGO*/

        .logo h1{
            margin: 0;
        }
        .logo h1 a{
            color: #17bebb;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Work Sans', sans-serif;
        }

        /*HERO*/
        .hero{
            position: relative;
            z-index: 0;
        }

        .hero .text{
            color: rgba(0,0,0,.3);
        }
        .hero .text h2{
            color: #000;
            font-size: 34px;
            margin-bottom: 15px;
            font-weight: 300;
            line-height: 1.2;
        }
        .hero .text h3{
            font-size: 24px;
            font-weight: 200;
        }
        .hero .text h2 span{
            font-weight: 600;
            color: #000;
        }


        /*PRODUCT*/
        .product-entry{
            display: block;
            position: relative;
            float: left;
            padding-top: 20px;
        }
        .product-entry .text{
            width: calc(100% - 125px);
            padding-left: 20px;
        }
        .product-entry .text h3{
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .product-entry .text p{
            margin-top: 0;
        }
        .product-entry img, .product-entry .text{
            float: left;
        }

        ul.social{
            padding: 0;
        }
        ul.social li{
            display: inline-block;
            margin-right: 10px;
        }

        /*FOOTER*/

        .footer{
            border-top: 1px solid rgba(0,0,0,.05);
            color: rgba(0,0,0,.5);
        }
        .footer .heading{
            color: #000;
            font-size: 20px;
        }
        .footer ul{
            margin: 0;
            padding: 0;
        }
        .footer ul li{
            list-style: none;
            margin-bottom: 10px;
        }
        .footer ul li a{
            color: rgba(0,0,0,1);
        }


        @media screen and (max-width: 500px) {


        }


    </style>


</head>

<body width='100%' style='margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;'>
<center style='width: 100%; background-color: #f1f1f1;'>
    <div style='display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;'>
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    <div style='max-width: 600px; margin: 0 auto;' class='email-container'>
        <!-- BEGIN BODY -->
        <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%' style='margin: auto;'>
            <tr>
                <td valign='top' class='bg_white' style='padding: 1em 2.5em 0 2.5em;'>
                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td class='logo' style='text-align: left;'>
                                <h1><a href='https://color-vintage.gazolinecommunication.com/'>Color Vintage</a></h1>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr><!-- end tr -->
            <tr>
                <td valign='middle' class='hero bg_white' style='padding: 2em 0 2em 0;'>
                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                        <tr>
                            <td style='padding: 0 2.5em; text-align: left;'>
                                <div class='text'>
                                    <h2>Recapitulatif de votre commande $numeroCommande depuis le site - COLOR VINTAGE</h2>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr><!-- end tr -->
            <tr>
                <table class='bg_white' role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr style='border-bottom: 1px solid rgba(0,0,0,.05);'>
                        <th width='80%' style='text-align:left; padding: 0 2.5em; color: #000; padding-bottom: 20px'>Item</th>
                        <th width='20%' style='text-align:right; padding: 0 2.5em; color: #000; padding-bottom: 20px'>Price</th>
                    </tr>";

                    foreach ($articles as $key => $value) {
                        $message .="
                        <tr style='border-bottom: 1px solid rgba(0,0,0,.05);'>
                        <td valign='middle' width='80%' style='text-align:left; padding: 0 2.5em;'>
                            <div class='product-entry'>
                                <a href=".$value['link']."><img src=https://color-vintage.gazolinecommunication.com".$value['image']."  style='width: 100px; max-width: 600px; height: auto; margin-bottom: 20px; display: block;'></a>
                                <div class='text'>
                                    <h3><a href=".$value['link'].">".$value['title']."</a></h3>
                                    <span>Prix unitaire: ".$value['prix']."</span>
                                    <p>Nombre: ".$value['nombre']."</p>
                                </div>
                            </div>
                        </td>
                        <td valign='middle' width='20%' style='text-align:left; padding: 0 2.5em;'>
                            <span class='price' style='color: #000; font-size: 20px;'>".$value['prixTotal']." &euro;</span>
                        </td>
                    </tr>";
                    }
                    $message .="
                    <tr>
                        <td valign='middle' style='text-align:left; padding: 1em 2.5em;'>
                            <p><a href=https://color-vintage.gazolinecommunication.com/node/$commandeID class='btn btn-primary'>Facture de la commande</a></p>
                        </td>
                    </tr>
                </table>
            </tr><!-- end tr -->
            <!-- 1 Column Text + Button : END -->
        </table>
        <div class='livraison'>
            <strong>Adresse du client</strong> <br>
            <strong>Nom : </strong>".$_SESSION['adresseLivraison']['name']." ".$_SESSION['adresseLivraison']['lastname']."<br>
            <strong>Tel : </strong>".$_SESSION['adresseLivraison']['name']."<br>
            <strong>Adresse : </strong>".$_SESSION['adresseLivraison']['adress']." ".$_SESSION['adresseLivraison']['adress_plus']."<br>
        </div>
        <h3>Adresse de Facturation</h3>
        <div class='livraison'>
            <strong>Adresse du client: </strong> <br>
            <strong>Nom : </strong>".$livraison['nom']." ".$livraison['prenom']."<br>
            <strong>Adresse : </strong>".$livraison['nom']."<br>
            <strong>Code Postal:". $livraison['Code']."</strong> <br>
            <strong>Ville : </strong>".$livraison['ville']."<br>
           
            <p style='margin-top:0;margin-right:0;font-size:13px;margin-bottom:0px;margin-left:0'>
                <strong> Date de livraison maximum estimee dans les 3 ou 4 prochains jours : </strong>
            </p>
        </div>
        <table align='center' role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%' style='margin: auto;'>
            <tr>
                <td valign='middle' class='bg_light footer email-section'>
                    <table>
                        <tr>
                            <td valign='top' width='33.333%' style='padding-top: 20px;'>
                                <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                                    <tr>
                                        <td style='text-align: left; padding-right: 10px;'>
                                            <h3 class='heading'>About</h3>
                                            <p>A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign='top' width='33.333%' style='padding-top: 20px;'>
                                <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                                    <tr>
                                        <td style='text-align: left; padding-left: 5px; padding-right: 5px;'>
                                            <h3 class='heading'>Contact Info</h3>
                                            <ul>
                                                <li><span class='text'>203 Fake St. Mountain View, San Francisco, California, USA</span></li>
                                                <li><span class='text'>+2 392 3929 210</span></a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign='top' width='33.333%' style='padding-top: 20px;'>
                                <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                                    <tr>
                                        <td style='text-align: left; padding-left: 10px;'>
                                            <h3 class='heading'>Useful Links</h3>
                                            <ul>
                                                <li><a href='#'>Home</a></li>
                                                <li><a href='#'>Account</a></li>
                                                <li><a href='#'>Wishlist</a></li>
                                                <li><a href='#'>Order</a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr><!-- end: tr -->
            <tr>
                <td class='bg_white' style='text-align: center;'>
                    <p>No longer want to receive these email? You can <a href='#' style='color: rgba(0,0,0,.8);'>Unsubscribe here</a></p>
                </td>
            </tr>
        </table>

    </div>
</center>
</body>
</html>
        ";
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "diouf@studiogazoline.com";
        $to = "diouf@studiogazoline.com";
        $subject = "Recapitulatif de votre commande $numeroCommande depuis le site - COLOR VINTAGE";
        $header = "From: $from \r\n";
        $header .= "Cc:diouf@studiogazoline.com \r\n";
       /* $header .= "Cc:cg@lundimatin.fr \r\n";*/
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        $result = mail($to,$subject,$message, $header);

        return $result;
    }

}