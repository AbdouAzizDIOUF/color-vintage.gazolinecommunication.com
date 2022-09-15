<?php

namespace Drupal\payment_module\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;


class PaymentController{

    public function generateFacture($id){

      $commande = Node::Load($id);
      $numeroCommande = $commande->get('field_numero_commande')->value;
      $link = Url::fromRoute('entity.node.canonical', ['node' => $id], ['absolute' => TRUE]);
      $pdf = new Pdf("https://color-vintage.gazolinecommunication.com/node/$id");
      // dump($pdf->saveAs("public://factures/facture_commande_$numeroCommande.pdf"));
      if (!$pdf->saveAs("public://factures/facture_commande_$numeroCommande.pdf")) {
        $error = $pdf->getError();
        dump($error);
        $response["error"] = $error;
        $response["status"] = 500;

      }

      $response["pdf"] = file_create_url("public://factures/facture_commande_$numeroCommande.pdf");
      //dump($commande);die;

      /*$date = $cr->field_date_reunion->value ?? '';
      //$date = date('Y-m-d', strtotime($date));
      $date = (new DateTime($date))->format('Y-m-d');

      $checkuser = appApiController::checkUser(1);
      $response = [];
      if($checkuser) {
          global $base_url;
          $pdf = new Pdf($base_url.'/node/'.$id);

          // var_dump($base_url.'/node/'.$id);die;
          // var_dump($pdf->saveAs('public://crs/cr_'.$id.'.pdf'));
          $response["status"] = 200;
          if (!$pdf->saveAs("public://crs/cr_{$cr->field_numero->value}_$date.pdf")) {
              $error = $pdf->getError();
              $response["error"] = $error;
              $response["status"] = 500;

              return new JsonResponse($response);
          }

          $response["pdf"] = file_create_url("public://crs/cr_{$cr->field_numero->value}_$date.pdf");
      }

      return new JsonResponse($response);*/
      $redirect = new RedirectResponse($response["pdf"]);
      return  $redirect->send();
    }

    public function paymentPaypal(Request $request)
    {
        if ($request->query->all()['methodepayment']==='paypal' || $_GET['paypal']=='true'){
            Drupal::service('payment_module.service')->payer();
        }

        if ($request->query->all()['methodepayment']==='stripe'){
            Drupal::service('payment_module.service')->payerStripe();
        }

    }

    public function monespace(){
        $commandesTmp = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'commande', 'field_client' => \Drupal::currentUser()->id()]);
        $commandesList = [];
        foreach ($commandesTmp as $commande) {
            $commandesList[] = [
                'id' => $commande->id(),
                'date' => date("d.m.Y", $commande->get('created')->value),
                'prixTotal' => $commande->get('field_somme_totale')->value,
                'numCommande' => $commande->get('field_numero_commande')->value,
                'link' => Url::fromRoute('entity.node.canonical', ['node' => $commande->id()], ['absolute' => TRUE])
            ];
        }

        $user = User::load(\Drupal::currentUser()->id());
        $userInfo = [
            'nom' => $user->get('field_nom')->value,
            'prenom' => $user->get('field_nom')->value,
            'email' => $user->get('mail')->value,
        ];


        $commandes = Drupal::service('payment_module.service')->getArticlesCommande();
        return [
            '#theme' => 'monespace',
            '#commandes' => ['commandesList' => $commandesList, 'commandes' => $commandes['commandes'], 'formations' => $commandes['formations']],
            '#userInfo' => $userInfo,
        ];
    }
}
