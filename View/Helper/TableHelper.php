<?php
/**
 * This helper adds the ability to make tables easier and faster
 *
 * @copyright Copyright (c) Avolans (http://avolans.nl)
 * @link http://github.com/JelmerD Github JelmerD
 * @package app.View.Helper
 * @version 1.0.0
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @author Jelmer Dröge <jelmer@avolans.nl>
 */
app::uses('AppHelper', 'View/Helper');

/**
 * Class TableHelper
 *
 * @package app.View.Helper
 * @property HtmlHelper $Html
 */
class TableHelper extends AppHelper
{

    /**
     * Helpers we use
     *
     * @var array
     */
    public $helpers = array('Html');

    /**
     * A cache of the head section.
     *
     * @var array
     */
    private $__head;

    /**
     * If set to true, the body is opened and may be closed
     *
     * @var bool
     */
    private $__bodyOpen;

    /**
     * Keep track of the row count
     *
     * @var int
     */
    private $__rowCount;


    /**
     * Initialise the Table
     *
     * @param array $options Options to parse to the `table` tag
     * @return string The table opening
     */
    public function create($options = array())
    {
        $this->__reset();
        $options = array_merge_recursive(array(
            'class' => 'table'
        ), $options);
        return $this->Html->tag('table', null, $options);
    }

    /**
     * Parse the `thead` section
     *
     * @param array $columns The columns to add in this `thead`
     * @param array $rowOptions The options to parse to the `tr` tag
     * @param array $options The options to parse to the `thead` tag
     * @return string The complete thead section
     */
    public function head($columns = array(), $rowOptions = array(), $options = array())
    {
        $this->__head = $columns;
        return $this->__bodyClose() . $this->Html->tag('thead', $this->__row($columns, $rowOptions, 'th'), $options);
    }

    /**
     * Open the `tbody` section
     *
     * This method will be called automatically when opening your first row. But for extra options you will need to call this manually
     *
     * @param array $options The options to parse to the `tbody` tag
     * @return string The opening of the `tbody`
     */
    public function body($options = array())
    {
        if ($this->__bodyOpen) {
            return null;
        }
        $this->__bodyOpen = true;
        return $this->Html->tag('tbody', null, $options);
    }

    /**
     * Parse a row
     *
     * @param array $columns The columns to add in this `tbody` tag
     * @param array $options The options to parse to this `tr` tag
     * @return string The complete row
     */
    public function row($columns = array(), $options = array())
    {
        $this->__rowCount++;
        return $this->body() . $this->__row($columns, $options);
    }

    /**
     * Parse the `tfoot` section (and close the `tbody` section)
     *
     * @param array $columns The columns to add in this `tfoot` tag
     * @param array $rowOptions The options to parse to the `tr` tag
     * @param array $options The options to parse to the `tfoot` tag
     * @return string The complete `tfoot` section
     */
    public function foot($columns = array(), $rowOptions = array(), $options = array())
    {
        return $this->__bodyClose() . $this->Html->tag('tfoot', $this->__row($columns, $rowOptions), $options);
    }

    /**
     * Close the table (and `tbody` if still needed)
     *
     * @return string
     */
    public function end()
    {
        return $this->__bodyClose() . '</table>';
    }

    /**
     * Parse one cell
     *
     * You can either parse a string or an array as value. If you parse a string, this will be used as the cell value.
     * If you parse an array, the first key (0) will be used as the value and the second key (1) will be used as the
     * options array for the `$tag`. This way you can parse options to the `td/th` tag.
     *
     * @param string|array $value The value to have in this cell
     * @param null $key The key used for this cell, if the same key in the `thead` section is `false`, it won't be parsed
     * @param string $tag The tag to use (either `td` or `th`)
     * @return null|string The complete cell, or null if the value at the same key in `thead` is set to `false`
     */
    public function cell($value, $key = null, $tag = 'td')
    {
        if (isset($this->__head[$key]) && $this->__head[$key] === false) {
            return null;
        }
        # if the value is an array, the user could have parsed a content and options key. Gives the user extra control.
        return $this->Html->tag(
            $tag,
            is_array($value) && array_key_exists(0, $value) ? $value[0] : $value,
            is_array($value) && array_key_exists(1, $value) ? $value[1] : array()
        );
    }

    /**
     * Parse a row with a certain cell tag
     *
     * @param array $columns The columns to parse in this `tr` tag
     * @param array $options The options to parse to the `tr` tag
     * @param string $tag The tag to use (either `td` or `th`)
     * @return string The complete `tr` tag with its values
     */
    private function __row($columns = array(), $options = array(), $tag = 'td')
    {
        $cells = null;
        foreach ($columns as $key => $value) {
            $cells .= $this->cell($value, $key, $tag);
        }
        return $this->Html->tag('tr', $cells, $options);
    }

    /**
     * Close the body if it's still open
     *
     * @return string
     */
    private function __bodyClose()
    {
        if ($this->__bodyOpen) {
            $this->__bodyOpen = false;
            return '</tbody>';
        }
        return null;
    }

    /**
     * Reset the tracking variables
     */
    private function __reset()
    {
        $this->__head = array();
        $this->__bodyOpen = false;
        $this->__rowCount = 0;
    }

}