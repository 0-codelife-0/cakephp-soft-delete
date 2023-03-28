<?php
namespace SoftDelete\ORM;

use Cake\ORM\Query as CakeQuery;

class Query extends CakeQuery
{
    /**
     * Cake\ORM\Query::triggerBeforeFind overwritten to add the condition `deleted IS NULL` to every find request
     * in order not to return soft deleted records.
     * If the query contains the option `withDeleted` the condition `deleted IS NULL` is not applied.
     */
    public function triggerBeforeFind(): void
    {
        if (!$this->_beforeFindFired && $this->_type === 'select') {
            parent::triggerBeforeFind();

            $aliasedField = $this
                ->getRepository()
                ->aliasField($this->getRepository()->getSoftDeleteField());
            if (in_array('withoutDeleted', $this->getOptions())) {
                $this->andWhere($aliasedField . ' IS NULL');
            } else if (in_array('onlyDeleted', $this->getOptions())){
                $this->andWhere($aliasedField . ' IS NOT NULL');
            }
        }
    }
}
