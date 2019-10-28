<?php

namespace Drupal\myrating\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;



class MyratingSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myrating_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['myrating.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('myrating.settings');
    $form['vote_message'] = [
      '#type' => 'textarea',
      '#title' => 'Текст микроразметки',
      '#rows' => 5,
      '#default_value' => !empty($config->get('vote_message')) ? $config->get('vote_message') : '',
      '#description' => htmlspecialchars('Пример: <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
  <span itemprop="itemReviewed" itemscope itemtype="http://schema.org/Organization"><span itemprop="name">Название сайта</span></span>.  
   Общий рейтинг: <span itemprop="ratingValue">[ratingValue]</span>/<span itemprop="bestRating">[bestRating]</span> 
   на основе <span itemprop="reviewCount">[reviewCount]</span> человек.
</div>'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('myrating.settings')
      ->set('vote_message', $form_state->getValue('vote_message'))
      ->save();
    parent::submitForm($form, $form_state);
  }


}
