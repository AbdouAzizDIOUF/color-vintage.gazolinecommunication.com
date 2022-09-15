<?php

namespace Drupal\gz_twig_extension;

use Drupal\file\Entity\File;
use Drupal\gz_pages\Controller\GzPagesController;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * @package Drupal\gz_twig_extension
 */
class TwigExtension extends \Twig_Extension
{

  public function getName()
  {
    return 'TwigFilter.gz_twig_extension';
  }

  public function getFunctions()
  {
    return [
      new \Twig_SimpleFunction('add_classes', [$this, 'addClasses']),
      new \Twig_SimpleFunction('get_content_file_from_fid', [$this, 'getContentFileFromFid']),
      new \Twig_SimpleFunction('get_content_file_url', [$this, 'getContentFileFromUrl']),
    ];
  }

  public function getFilters()
  {
    return [
      new \Twig_SimpleFilter('date_format', [$this, 'dateFormat']),
      new \Twig_SimpleFilter('youtube_id', [$this, 'youtubeId']),
      new \Twig_SimpleFilter('youtube_thumbnail', [$this, 'youtubeThumbnail']),
      new \Twig_SimpleFilter('chiffre_format', [$this, 'chiffreFormat']),
      new \Twig_SimpleFilter('mime_icon', [$this, 'mimeIcon']),
      new \Twig_SimpleFilter('base64', [$this, 'base64']),
      new \Twig_SimpleFilter('summary', [$this, 'summary']),
      new \Twig_SimpleFilter('trim', [$this, 'trim2']),
      new \Twig_SimpleFilter('base64_encode', [$this, 'base64Encode']),
    ];
  }

  /**
   * Ajoute des classes CSS à des éléments
   * HTML en fonction de leur tag.
   *
   * @param string $html
   * @param array $classes
   *
   * @return mixed
   */
  public function addClasses($html, $classes)
  {
    $doc = new \DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);

    foreach ($classes as $tag => $class) {
      foreach ($doc->getElementsByTagName($tag) as $item) {
        if ($item instanceof \DOMElement) {
          $item->setAttribute('class', $class);
        }
      }
    }

    return str_replace(['<?xml encoding="utf-8" ?>', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body>', '</body></html>'], '', $doc->saveHTML());
  }

  /**
   * Formate un DateTime via le service
   * Drupal (permet la traduction des
   * mois et jours).
   *
   * @param \DateTime $date
   * @param string $format
   *
   * @return string
   */
  public function dateFormat(\DateTime $date, $format = 'd M. Y')
  {
    return \Drupal::service('date.formatter')->format($date->getTimestamp(), 'custom', $format);
  }

  /**
   * Récupère l'ID d'une vidéo Youtube
   * depuis son URL.
   *
   * @param string $url
   *
   * @return string
   */
  public function youtubeId($url) {
    // var_dump($url);die;
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    return $match[1];
  }

  public function youtubeThumbnail($url) {
    return 'https://img.youtube.com/vi/' . $this->youtubeId($url) . '/hqdefault.jpg';
  }

  /**
   * Formate un nombre.
   *
   * @param string $chiffre
   *
   * @return string
   */
  public function chiffreFormat($chiffre) {
    return (substr($chiffre, -2) == '00') ? number_format($chiffre, 0, ',', ' ') : number_format($chiffre, 2, ',', ' ');
  }

  public function mimeIcon($mime) {
    $icon = 'fa-file';

    switch ($mime) {
      // PDF.
      case 'application/pdf':
        $icon .= '-pdf';
        break;
      // DOC / DOCX / ODT / RTF.
      case 'application/msword':
      case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      case 'application/vnd.oasis.opendocument.text':
      case 'application/rtf':
        $icon .= '-word';
        break;
      // GIF / JPEG/JPG / PNG.
      case 'image/gif':
      case 'image/jpeg':
      case 'image/png':
        $icon .= '-image';
        break;
      // ODP / PPT / PPTX.
      case 'application/vnd.oasis.opendocument.presentation':
      case 'application/vnd.ms-powerpoint':
      case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
        $icon .= '-powerpoint';
        break;
      // ODS / XLS / XLSX.
      case 'application/vnd.oasis.opendocument.spreadsheet':
      case 'application/vnd.ms-excel':
      case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
        $icon .= '-excel';
        break;
      // TXT.
      case 'text/plain':
        $icon .= '-alt';
        break;
      // MP4 / MKV / AVI / MPG / MOV / FLV / MPEG / OGV.
      case 'video/mp4':
      case 'video/x-msvideo':
      case 'video/x-flv':
      case 'video/quicktime':
      case 'video/x-matroska':
      case 'video/mpeg':
      case 'video/ogg':
        $icon .= '-video';
        break;
    }

    return $icon;
  }

  /**
   * Encode une chaine en
   * base64.
   *
   * @param string $string
   *
   * @return string
   */
  public function base64($string) {
    return base64_encode($string);
  }

  /**
   * Retourne le résumé d'une
   * chaine.
   *
   * @param NodeInterface $node
   * @param string $field
   * @param int $size
   *
   * @return string
   */
  public function summary(NodeInterface $node, $field, $size = 80) {
    return (empty($node->get($field)->summary)) ? text_summary($node->get($field)->value, $node->get($field)->format, $size) : $node->get($field)->summary;
  }

  /**
   * GET content of a file by the target id (fid)
   *
   * @param int $target_id
   *
   * @return string
   */
  public function getContentFileFromFid($target_id) {
    $file = \Drupal\file\Entity\File::load($target_id);
    if(!is_null($file)){
      $uri = $file->getFileUri();
      //$content_file = file_get_contents(str_replace('http', 'https', file_create_url($uri)));
      $content_file = file_get_contents($uri);
      return $content_file;
    }else{
      return false;
    }
  }

  /**
   * GET content of a file by the url
   *
   * @param string $url
   *
   * @return string
   */
  public function getContentFileFromUrl($url) {
    if(!is_null($url)){
      $content_file = file_get_contents('https://'.$_SERVER['SERVER_NAME'] . $url);
      return $content_file;
    }else{
      return false;
    }
  }

  /**
   * remove space from string
   *
   * @param string $string
   *
   * @return string
   */
  public function trim2($string) {
    if(!is_null($string)){
      $string_trimmed = preg_replace("/[\n\r]/","",$string);
      $string_trimmed = trim($string_trimmed);
      return $string_trimmed;
    } else {
      return false;
    }
  }

  /**
   * Encode string to base64
   *
   * @param string $string
   *
   * @return string
   */
  public function base64Encode($string) {
    if(!is_null($string)){
      $string_base64 = base64_encode($string);
      return $string_base64;
    } else {
      return false;
    }
  }
}