<?php

class OCMailAlert extends eZPersistentObject
{

    /**
     * Constructor
     *
     * @param array $row
     */
    public function __construct(array $row = array())
    {
        parent::eZPersistentObject($row);
    }

    /**
     * @see kernel/classes/eZPersistentObject::definition()
     * @return array
     */
    public static function definition()
    {
        return array(
            'fields' => array(
                'id' => array(
                    'name' => 'id',
                    'datatype' => 'integer',
                    'default' => null,
                    'required' => true
                ),
                'label' => array(
                    'name' => 'label',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'frequency' => array(
                    'name' => 'frequency',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'query' => array(
                    'name' => 'query',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'condition' => array(
                    'name' => 'condition',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'condition_value' => array(
                    'name' => 'condition_value',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'recipients' => array(
                    'name' => 'recipients',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'subject' => array(
                    'name' => 'subject',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'body' => array(
                    'name' => 'body',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => true
                ),
                'last_call' => array(
                    'name' => 'last_call',
                    'datatype' => 'integer',
                    'default' => 0,
                    'required' => false
                ),
                'last_log' => array(
                    'name' => 'last_log',
                    'datatype' => 'string',
                    'default' => null,
                    'required' => false
                ),
            ),
            'keys' => array('id'),
            'increment_key' => 'id',
            'class_name' => 'OCMailAlert',
            'name' => 'ocmailalert',
            'function_attributes' => array(
                'recipients_address' => 'getRecipientList',
                'condition_operator' => 'getConditionOperator'
            ),
        );
    }

    /**
     * Generic toString method
     */
    public function __toString()
    {
        return (string)$this->attribute('label');
    }

    /**
     * Fetches scheduled alert
     *
     * @param int $offset Offset. Default is 0.
     * @param int $limit Limit. Default is null. If null, all $alerts items will be returned
     * @param array $conds Additional conditions for fetch. See {@link eZPersistentObject::fetchObjectList()}. Default is null
     *
     * @return OCMailAlert[]
     */
    public static function fetchList($offset = 0, $limit = null, $conds = null)
    {
        if (!$limit) {
            $aLimit = null;
        } else {
            $aLimit = array('offset' => $offset, 'length' => $limit);
        }

        $sort = array('id' => 'asc');
        $alerts = self::fetchObjectList(self::definition(), null, $conds, $sort, $aLimit);

        return $alerts;
    }

    /**
     * Fetches a scheduled alert by its ID
     *
     * @param int $alertID
     *
     * @return OCMailAlert
     */
    public static function fetch($alertID)
    {
        $alert = self::fetchObject(self::definition(), null, array('id' => $alertID));

        return $alert;
    }

    /**
     * Sets attributes from an associative array (key = attribute name)
     *
     * @param array $attributes
     */
    public function fromArray(array $attributes)
    {
        foreach ($attributes as $attributeName => $attribute) {
            if ($this->hasAttribute($attributeName)) {
                $this->setAttribute($attributeName, $attribute);
            }
        }
    }

    public function getRecipientList()
    {
        return OCMailAlertUtils::splitMailAddresses($this->attribute('recipients'));
    }

    public function getConditionOperator()
    {
        return OCMailAlertUtils::conditionOperatorName($this->attribute('condition'));
    }

}
