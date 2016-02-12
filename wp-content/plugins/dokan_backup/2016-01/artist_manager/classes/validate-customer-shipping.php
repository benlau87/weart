<?php

/**
 * Class Validate_Customer_Shipping
 * @author Benedikt Laufer
 */
class Validate_Customer_Shipping
{

    /**
     * @var null|WP_Error
     */
    private $_error = null;

    /**
     * @return null|WP_Error
     */
    public function getWpError()
    {
        if ($this->_error == null) {
            $this->_error = new WP_Error();
        }
        return $this->_error;
    }

    public function printWpError()
    {
        if (is_wp_error($this->getWpError())) {
            $return = array();
            $i = 0;
            foreach ($this->getWpError()->get_error_messages() as $error) {
                $return[$i] .= '<div>';
                $return[$i] .= '<strong>ERROR</strong>:';
                $return[$i] .= $error . '<br/>';
                $return[$i] .= '</div>';
                $i++;
            }
            return $return;
        }
        return false;
    }

    /**
     * @param string $code
     * @param string $message
     * @return null|WP_Error
     */
    public function addError($code, $message)
    {
        $this->getWpError()->add($code, $message);
        return $this->getWpError();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return is_wp_error($this->_error);
    }

    /**
     * Checks if the pickup is allowed for the customer
     * @param array $store_settings
     * @param array $user_meta
     * @return bool|void
     * @throws Exception
     */
    public function isPickupAllowed($store_settings, $user_meta)
    {
        if (!is_array($store_settings) || !is_array($user_meta)) {
            throw new \Exception('Please provide valid arguments for ' . __METHOD__);
        }

        if (reset($user_meta['first_name']) == "") {
            return $this->addError('customer_firstname', sprintf(__('Du musst einen Vornamen angeben.', 'waa'), 'customer_firstname'));
        }

        if (reset($user_meta['last_name']) == "") {
            return $this->addError('customer_firstname', sprintf(__('Du musst einen Nachnamen angeben.', 'waa'), 'customer_firstname'));
        }

        $required_fields = array(
            'street_1',
            'city',
            'zip',
            'country',
        );
        foreach ($required_fields as $key) {
            if ($store_settings['address'][$key] == '') {
                $code = 'waa_address[' . $key . ']';
                return $this->addError($code, sprintf(__('Address field for %s is required', 'waa'), $key));
            }
        }

        return false;
    }

}