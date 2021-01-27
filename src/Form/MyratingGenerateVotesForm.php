<?php

namespace Drupal\myrating\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class MyratingGenerateVotesForm extends FormBase {


  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The vote storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $voteStorage;

  /**
   * The block_content entity storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockContentStorage;


  /**
   * Constructs.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->voteStorage = $entity_type_manager->getStorage('vote');
    $this->blockContentStorage = $entity_type_manager->getStorage('block_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myrating_generate_votes_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $blocks = $this->blockContentStorage->loadByProperties([
      'type' => 'myrating',
    ]);
    if (empty($blocks)) {
      $form['empty_blocks'] = [
        '#markup' => '<p>Нет ниодного блока с типом myrating</p>',
      ];
      return $form;
    }

    $options = [];
    foreach ($blocks as $block) {
      $options[$block->id()] = $block->label();
    }

    $form['count'] = [
      '#type' => 'number',
      '#title' => 'Кол-во голосов для генерации',
      '#default_value' => 10,
      '#required' => TRUE,
    ];
    $form['value'] = [
      '#type' => 'number',
      '#title' => 'Процент балов',
      '#default_value' => 100,
      '#description' => 'Кратно кол-ву звезд',
      '#required' => TRUE,
    ];
    $form['entity_id'] = [
      '#title' => 'Блок рейтинга',
      '#type' => 'select',
      '#options' => $options,
      '#empty_option' => '- Select -',
      '#required' => TRUE,
      '#default_value' => NULL,
    ];
    if (count($options) == 1) {
      $options = array_keys($options);
      $form['entity_id']['#default_value'] = $options[0];
      $form['entity_id']['#access'] = FALSE;
    }
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Генерировать',
    ];
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $count = $form_state->getValue('count');
    $value = $form_state->getValue('value');
    $entity_id = $form_state->getValue('entity_id');
    if ($count && $entity_id && $value && $value <= 100) {
      $time = time();
      for ($i = 1; $i <= $count; $i++) {
        $vote = $this->voteStorage->create(['type' => 'vote']);
        $vote->setVotedEntityType('block_content');
        $vote->setVotedEntityId($entity_id);
        $vote->setValueType('percent');
        $vote->setValue($value);
        $vote->setOwnerId(0);
        // $vote->setCreatedTime($time - $i * 86400);
        $vote_source = hash('sha256', $time - $i);
        $vote->setSource($vote_source); // Нужно что бы не удаляло голоса при одинаковом айпи
        $vote->save();
      }
    }
    $this->messenger()->addStatus('Голоса успешно добавлены');
  }


}
