<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller;

use BEdita\API\Model\Action\UpdateAssociatedAction;
use BEdita\Core\Model\Action\AddAssociatedAction;
use BEdita\Core\Model\Action\DeleteEntityAction;
use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\Model\Action\ListAssociatedAction;
use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Model\Action\RemoveAssociatedAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Action\SetAssociatedAction;
use Cake\Core\InstanceConfigTrait;
use Cake\Network\Exception\ConflictException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Base controller for CRUD actions on generic resources.
 *
 * @since 4.0.0
 */
abstract class ResourcesController extends AppController
{

    use InstanceConfigTrait;

    /**
     * Configuration.
     *
     * Available configurations are:
     *  - `allowedAssociations`: an associative array of relationships names, and
     *      an array of allowed resource types for that relationship.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'allowedAssociations' => [],
    ];

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        if (isset($this->JsonApi)) {
            $this->JsonApi->setConfig('resourceTypes', [Inflector::underscore($this->name)]);

            if ($this->request->getParam('action') === 'relationships') {
                $this->JsonApi->setConfig(
                    'resourceTypes',
                    $this->getConfig(sprintf('allowedAssociations.%s', $this->request->getParam('relationship')))
                );
                $this->JsonApi->setConfig('clientGeneratedIds', true);
            }
        }

        $this->Table = TableRegistry::get($this->modelClass);
    }

    /**
     * Find the association corresponding to the relationship name.
     *
     * @param string $relationship Relationship name.
     * @return \Cake\ORM\Association
     * @throws \Cake\Network\Exception\NotFoundException Throws an exception if no association could be found.
     */
    protected function findAssociation($relationship)
    {
        $relationship = Inflector::underscore($relationship);
        if (array_key_exists($relationship, $this->getConfig('allowedAssociations'))) {
            $associations = $this->Table->associations();
            foreach ($associations as $association) {
                if ($association->property() === $relationship) {
                    return $association;
                }
            }
        }

        throw new NotFoundException(__d('bedita', 'Relationship "{0}" does not exist', $relationship));
    }

    /**
     * List and add entities.
     *
     * This action represents a collection of resources.
     * If the request is a `POST` request, this action creates a new resource.
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->request->allowMethod(['get', 'post']);

        if ($this->request->is('post')) {
            // Add a new entity.
            $entity = $this->Table->newEntity();
            $action = new SaveEntityAction(['table' => $this->Table]);

            $data = $this->request->getData();
            $entity = $action(compact('entity', 'data'));

            return $this->response
                ->withStatus(201)
                ->withHeader(
                    'Location',
                    Router::url(
                        [
                            '_name' => 'api:resources:resource',
                            'controller' => $this->name,
                            'id' => $entity->id,
                        ],
                        true
                    )
                );
        }

        // List existing entities.
        $action = new ListEntitiesAction(['table' => $this->Table]);
        $query = $action();

        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);

        return null;
    }

    /**
     * View and manage single entities.
     *
     * This action represents a single resource.
     * If the request is a `PATCH` request, this action updates an existing resource.
     * If the request is a `DELETE` request, this action deletes an existing resource.
     *
     * @param mixed $id Entity ID.
     * @return \Cake\Network\Response|null
     */
    public function resource($id)
    {
        $this->request->allowMethod(['get', 'patch', 'delete']);

        $action = new GetEntityAction(['table' => $this->Table]);
        $entity = $action(['primaryKey' => $id]);

        if ($this->request->is('delete')) {
            // Delete an entity.
            $action = new DeleteEntityAction(['table' => $this->Table]);

            if (!$action(compact('entity'))) {
                throw new InternalErrorException(__d('bedita', 'Delete failed'));
            }

            return $this->response
                ->withHeader('Content-Type', $this->request->contentType())
                ->withStatus(204);
        }

        if ($this->request->is('patch')) {
            // Patch an existing entity.
            if ($this->request->getData('id') !== $id) {
                throw new ConflictException(__d('bedita', 'IDs don\'t match'));
            }

            $action = new SaveEntityAction(['table' => $this->Table]);

            $data = $this->request->getData();
            $entity = $action(compact('entity', 'data'));
        }

        $this->set(compact('entity'));
        $this->set('_serialize', ['entity']);

        return null;
    }

    /**
     * Paginated list of related resources.
     *
     * This action represents a collection of related resources.
     *
     * @return void
     */
    public function related()
    {
        $this->request->allowMethod(['get']);

        $relationship = $this->request->getParam('relationship');
        $relatedId = $this->request->getParam('related_id');

        $association = $this->findAssociation($relationship);

        $action = new ListAssociatedAction(compact('association'));
        $query = $action->execute(['primaryKey' => $relatedId]);

        $data = $this->paginate($query);

        $this->set(compact('data'));
        $this->set('_serialize', ['data']);
    }

    /**
     * View and manage relationships.
     *
     * This action represents a collection of relationships.
     * If the request is a `PATCH` request, this action completely replaces the set of existing relationships.
     * If the request is a `POST` request, this action adds new relationships.
     * If the request is a `DELETE` request, this action deletes existing relationships.
     *
     * @return \Cake\Network\Response|null
     */
    public function relationships()
    {
        $this->request->allowMethod(['get', 'post', 'patch', 'delete']);

        $id = $this->request->getParam('id');
        $relationship = $this->request->getParam('relationship');

        $association = $this->findAssociation($relationship);

        switch ($this->request->getMethod()) {
            case 'PATCH':
                $action = new SetAssociatedAction(compact('association'));
                break;

            case 'POST':
                $action = new AddAssociatedAction(compact('association'));
                break;

            case 'DELETE':
                $action = new RemoveAssociatedAction(compact('association'));
                break;

            case 'GET':
            default:
                $action = new ListAssociatedAction(compact('association'));
                $data = $action(['primaryKey' => $id, 'list' => true]);

                if ($data instanceof Query) {
                    $data = $this->paginate($data);
                }

                $this->set(compact('data'));
                $this->set([
                        '_type' => $relationship,
                        '_serialize' => ['data'],
                    ]);

                return null;
        }

        $action = new UpdateAssociatedAction(compact('action') + ['request' => $this->request]);
        $count = $action(['primaryKey' => $id]);

        if ($count === false) {
            throw new InternalErrorException(__d('bedita', 'Could not update relationship "{0}"', $relationship));
        }

        if ($count === 0) {
            return $this->response
                ->withHeader('Content-Type', $this->request->contentType())
                ->withStatus(204);
        }

        $this->set(['_serialize' => []]);

        return null;
    }
}