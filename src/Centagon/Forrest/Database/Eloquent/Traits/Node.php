<?php

namespace Centagon\Forrest\Database\Eloquent\Traits;

/*
 * This file is part of the Build package.
 *
 * (c) Centagon <contact@centagon.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Node
{

    public static function bootNodeTrait()
    {
        static::observe(new NodeObserver);
    }

    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(get_class($this), $this->getParentColumnName())
            ->setModel($this);
    }

    /**
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(get_class($this), $this->getParentColumnName())
            ->setModel($this);
    }

    /**
     * @return Builder
     */
    public function ancestors()
    {
        $leftColumn = $this->getLeftColumnName();
        $rightColumn = $this->getRightColumnName();

        return $this
            ->where($leftColumn, '<', $this->getAttribute($leftColumn))
            ->where($rightColumn, '>', $this->getAttribute($rightColumn));
    }

    /**
     * Get the ancestors and self.
     *
     * @return Builder
     */
    public function ancestorsAndSelf()
    {
        return $this->ancestors()->orWhere($this->getKeyName(), $this->getKey());
    }

    public function neighbors()
    {
        // TODO
    }

    public function neighborsAndSelf()
    {
        // TODO
    }

    /**
     * Determine that this is a leaf node.
     *
     * @return bool
     */
    public function isLeaf()
    {
        return $this->children->count() === 0;
    }

    /**
     * Determine that this is the root node.
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->getAttribute($this->getParentColumnName()) === null;
    }

    /**
     * Get the root node.
     *
     * @return Node
     */
    public function getRoot()
    {
        $node = $this;

        while ($parent = $node->parent) {
            $node = $parent;
        }

        return $node;
    }

    /**
     * Determine that this is a child node.
     *
     * @return bool
     */
    public function isChild()
    {
        return $this->getAttribute($this->getParentColumnName()) !== null;
    }

    /**
     * Check the node for children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    public function getDepth()
    {
        // TODO
    }

    public function getHeight()
    {
        // TODO
    }

    /**
     * @return string
     */
    protected function getParentColumnName()
    {
        return 'parent_id';
    }

    public function getLeftColumnName()
    {
        return '_lft';
    }

    public function getRightColumnName()
    {
        return '_rgt';
    }
}