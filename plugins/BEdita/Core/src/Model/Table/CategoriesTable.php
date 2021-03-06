<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Categories Model
 *
 * @property \BEdita\Core\Model\Table\ObjectTypesTable&\Cake\ORM\Association\BelongsTo $ObjectTypes
 * @property \BEdita\Core\Model\Table\CategoriesTable&\Cake\ORM\Association\BelongsTo $ParentCategories
 * @property \BEdita\Core\Model\Table\CategoriesTable&\Cake\ORM\Association\HasMany $ChildCategories
 * @property \BEdita\Core\Model\Table\ObjectCategoriesTable&\Cake\ORM\Association\HasMany $ObjectCategories
 *
 * @method \BEdita\Core\Model\Entity\Category get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Category newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Category findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CategoriesTable extends CategoriesTagsBaseTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'className' => 'BEdita/Core.ObjectTypes'
        ]);
        $this->belongsTo('ParentCategories', [
            'className' => 'BEdita/Core.Categories',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildCategories', [
            'className' => 'BEdita/Core.Categories',
            'foreignKey' => 'parent_id'
        ]);
        $this->addBehavior('Tree', [
            'left' => 'tree_left',
            'right' => 'tree_right',
        ]);
        $this->hasMany('ObjectCategories', [
            'foreignKey' => 'category_id',
            'className' => 'BEdita/Core.ObjectCategories'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $this->validationRules($validator);

        $validator
            ->integer('tree_left')
            ->allowEmptyString('tree_left');

        $validator
            ->integer('tree_right')
            ->allowEmptyString('tree_right');

        $validator
            ->boolean('enabled')
            ->notEmptyString('enabled');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(
            ['object_type_id'],
            'ObjectTypes',
            ['allowNullableNulls' => true]
        ));
        $rules->add($rules->existsIn(
            ['parent_id'],
            'ParentCategories',
            ['allowNullableNulls' => true]
        ));

        return $rules;
    }

    /**
     * Add `object_typ_id` condition and remove some fields when retrieved as association.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\ORM\Query $query Query object instance.
     * @param \ArrayObject $options Options array.
     * @param bool $primary Primary flag.
     * @return void
     */
    public function beforeFind(Event $event, Query $query, \ArrayObject $options, $primary)
    {
        $query->andWhere([$this->aliasField('object_type_id') . ' IS NOT NULL']);
        if ($primary) {
            return;
        }
        $this->removeFields($query);
    }
}
