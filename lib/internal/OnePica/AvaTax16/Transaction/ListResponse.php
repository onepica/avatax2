<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax16
 * @copyright Copyright (c) 2016 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax16\Transaction;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Transaction\ListResponse
 *
 * @method bool getHasError()
 * @method setHasError(bool $value)
 * @method array getErrors()
 * @method setErrors(array $value)
 * @method array getItems()
 * @method setItems(array $value)
 */
class ListResponse extends Part
{
    /**
     * Has error
     *
     * @var bool
     */
    protected $_hasError = false;

    /**
     * Errors
     *
     * @var array
     */
    protected $_errors;

    /**
     * List items
     *
     * @var \OnePica\AvaTax16\Transaction\ListItemResponse[]
     */
    protected $_items;

    /**
     * Fill data from object
     *
     * @param \StdClass|array $data
     * @return $this
     */
    public function fillData($data)
    {
        $result = array();
        if (is_array($data)) {
            foreach ($data as $dataItem) {
                $calculationListItem = new ListItemResponse();
                $result[] = $calculationListItem->fillData($dataItem);
            }
        }
        $this->setItems($result);
    }
}
