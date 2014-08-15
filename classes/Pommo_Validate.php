<?php

/**
 *  Original Code Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 *  released originally under GPLV2
 *
 *  This file is part of poMMo.
 *
 *  poMMo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  poMMo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Pommo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This fork is from https://github.com/soonick/poMMo
 *  Please see docs/contribs for Contributors
 *
 */
class Pommo_Validate
{

    private $_postToValidate = array();
    private $_data = array();
    private $_dataPasswordMatch = array();
    private $_errors = array();
    private $_currentValidationError = '';

    /**
     * 	setPost
     * 	Recieves the incoming post into the array
     *
     * 	@param	array	$post - Incoming post
     *
     * 	@return	void
     */
    public function setPost($post)
    {
        //clear the arrays.. precautionary
        unset($this->_data);
        unset($this->post);
        unset($this->_errors);
        unset($this->dataPasswordMatch);

        $this->_postToValidate = $post;
    }

    /**
     * 	addData
     * 	Adds Data to the array to be checked
     *
     * 	@param	string	$name - Name of the input type (ie ID='name')
     *	@param	string  $type - Type to check. Valid Values
     *                          Email
     *                          date
     *                          dateTime
     *                          Time
     *                          Url
     *							matchRegex
     *                          Other (ie if you just want an empty check)
     *	@param	boolean $isemptyAllowed - Is input allowed to be empty
     *	@param	string	$regex - if matchRegex was choosen in $type, then this
     *					is the regex that is going to be checked
     *
     *  @return	void
     */
    public function addData($name, $type, $isEmptyAllowed, $regex = null)
    {
        $array = array($name, $type, $isEmptyAllowed, $regex);
        $this->_data[] = $array;
    }

    /**
     * 	addPasswordMatch
     * 	Adds Data to the password match array to be checked
     *
     * 	@param	string	$name - Name of the input type (ie ID='name')
     *          string  $name2 - Name of the 2nd input type
     *  @return	void
     */
    public function addPasswordMatch($name1, $name2)
    {
        $array = array($name1, $name2);
        $this->_dataPasswordMatch[] = $array;
    }

    /**
     * 	checkData
     * 	Runs the checks on the data fields and password matches
     *
     * 	@param	void
     *
     *  @return	boolean  True if NO errors
     */
    public function checkData()
    {
        $this->_errors = null;
        $emptyMessage = _('Cannot be empty.');
        $passMatchMessage = _('Passwords must match.');

        //Check the data fields
        foreach ($this->_data as $array) {
            $name = $array[0];
            $type = $array[1];
            $isEmptyAllowed = $array[2];

            //Check if empty and allowed to be empty
            if (empty($this->_postToValidate[$name])) {
                if (!$isEmptyAllowed) {
                    $this->_errors[$name] = $emptyMessage;
                }
            } else {
                $functionName = 'validate'.$type;
                $value = $this->_postToValidate[$name];
                if ($type != 'Other') {
                    if ('matchRegex' == $type) {
                        $result = $this->{$functionName}($array[3], $value);
                    } else {
                        $result = $this->{$functionName}($value);
                    }
                    if (!$result) {
                        $this->_errors[$name] = $this->_currentValidationError;
                    }
                }
            }
        }

        //Check the fields that must match
        foreach ($this->_dataPasswordMatch as $i => $value) {
            $array = $this->_dataPasswordMatch[$i];
            $name1 = $array[0];
            $name2 = $array[1];

            if (!empty($this->_postToValidate[$name2])) {
                if ($this->_postToValidate[$name1] != $this->_postToValidate[$name2]) {
                    $this->_errors[$name2] = $passMatchMessage;
                }
            } else {
                $this->_errors[$name2] = $emptyMessage;
            }
        }

        //Lets calling function know if there were errors.
        if (empty($this->_errors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 	getErrors
     * 	Returns what the validation errors were
     *
     * 	@param	void
     *
     *  @return	array Array containing all error messages
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * 	subscriberData
     * 	Validates supplied subscriber data against fields
     *
     * 	@param	array  $in - The subscriber Data
     *          array  $p  - A parameter array with values as follows :-
     *                       boolean prune - if true, prune the data array
     *                          (passed by reference) to only recognized/checked
     *                          fields
     *                       boolean ignore - if true, invalid fields will be pruned
     *                          from $in array -- no error thrown
     *                       boolean ignoreInactive - if true, invalid inactive
     *                          fields will be pruned from $in array - no error
     *                          thrown
     *                       boolean active - if true, only check data against
     *                          active fields. Typically true if subscribing via
     *                          form, false if admin importing.
     *                       boolean skipReq - if true, skip the required check
     *                          AND empty fields.
     *                       boolean log - if true, log invalid fields as error.
     *                          Typically true if subscribing via form, false if
     *                          admin importing.
     *
     *  @return	boolean  validation status
     *
     *  NOTE: has the MAGIC FUNCTIONALITY of converting date field input
     *     to a UNIX TIMESTAMP. This is necessary for quick SQL comparisson of dates, etc.
     *  NOTE: has the MAGIC FUNCTINALITY of changing "true"/"false" to checkbox "on"/off equivelent
     *  NOTE: has the MAGIC FUNCTIONALITY of trimming leading and trailing whitepace
     *  NOTE: has the MAGIC FUNCTIONALITY of shortening value to 60 characters (or 255 if a comment type)
     *  TODO -> should fields be passed by reference? e.g. are they usually
     *  already available when subscriberData() is called?
     */
    public static function subscriberData(&$in, $p = array())
    {
        $defaults = array(
            'prune' => true,
            'active' => true,
            'log' => true,
            'ignore' => false,
            'ignoreInactive' => true,
            'skipReq' => false);
        $p = Pommo_Api::getParams($defaults, $p);

        require_once(Pommo::$_baseDir.'classes/Pommo_Fields.php');
        $logger = Pommo::$_logger;

        $fields = Pommo_Fields::get(array('active' => $p['active']));

        $valid = true;
        foreach ($fields as $id => $field) {
            $inactive = ($field['active'] == 'on') ? false : true;

            if (!isset($in[$id]) && $p['skipReq'])
                continue;
            $in[$id] = @trim($in[$id]);

            if (empty($in[$id])) {
                unset($in[$id]); // don't include blank values
                if ($field['required'] == 'on') {
                    if ($p['log'])
                        $logger->addErr(sprintf(Pommo::_T('%s is a required field.'), $field['prompt']));
                    $valid = false;
                }
                continue;
            }

            // shorten
            $in[$id] = substr($in[$id], 0, 255);

            switch ($field['type'])
            {
                case "checkbox":
                    if (strtolower($in[$id]) == 'true')
                        $in[$id] = 'on';
                    if (strtolower($in[$id]) == 'false')
                        $in[$id] = '';
                    if ($in[$id] != 'on' && $in[$id] != '') {
                        if ($p['ignore'] || ($inactive && $p['ignoreInactive'])) {
                            unset($in[$id]);
                            break;
                        }
                        if ($p['log'])
                            $logger->addErr(sprintf(Pommo::_T('Illegal input for field %s.'), $field['prompt']));
                        $valid = false;
                    }
                    break;
                case "multiple":
                    if (is_array($in[$id])) {
                        foreach ($in[$id] as $key => $val)
                            if (!in_array($val, $field['array'])) {
                                if ($p['ignore']
                                        || ($inactive &&
                                        $p['ignoreInactive'])) {
                                    unset($in[$id]);
                                    break;
                                }
                                if ($p['log']) {
                                    $logger->addErr(
                                        sprintf(
                                            Pommo::_T(
                                                'Illegal input for field %s.'
                                            ),
                                            $field['prompt']
                                        )
                                    );
                                }
                                $valid = false;
                            }
                    } elseif (!in_array($in[$id], $field['array'])) {
                        if ($p['ignore'] ||
                                ($inactive && $p['ignoreInactive'])) {
                            unset($in[$id]);
                            break;
                        }
                        if ($p['log']) {
                            $logger->addErr(
                                sprintf(
                                    Pommo::_T(
                                        'Illegal input for field %s.'
                                    ),
                                    $field['prompt']
                                )
                            );
                        }
                        $valid = false;
                    }
                    break;
                // convert date to timestamp [float; using adodb time library]
                case "date":

                    if (is_numeric($in[$id]))
                        $in[$id] = Pommo_Helper::timeToStr($in[$id]);

                    $in[$id] = Pommo_Helper::timeFromStr($in[$id]);

                    if (!$in[$id]) {
                        if ($p['ignore'] ||
                                ($inactive && $p['ignoreInactive'])) {
                            unset($in[$id]);
                            break;
                        }
                        if ($p['log']) {
                            $logger->addErr(
                                sprintf(
                                    Pommo::_T(
                                        'Field (%s) must be a date ('
                                        . Pommo_Helper::timeGetFormat() . ').'
                                    ),
                                    $field['prompt']
                                )
                            );
                        }
                        $valid = false;
                    }
                    break;
                case "number":
                    if (!is_numeric($in[$id])) {
                        if ($p['ignore'] ||
                                ($inactive && $p['ignoreInactive'])) {
                            unset($in[$id]);
                            break;
                        }
                        if ($p['log']) {
                            $logger->addErr(
                                sprintf(
                                    Pommo::_T('Field (%s) must be a number.'),
                                    $field['prompt']
                                )
                            );
                        }
                        $valid = false;
                    }
                    break;
            }
        }
        // prune
        if ($p['prune'])
            $in = Pommo_Helper::arrayIntersect($in, $fields);

        return $valid;
    }

    /**
     * 	validateEmail
     * 	Validates an E-mail address
     *
     * 	@param  string	$email.- E-mail to validate
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validateEmail($email)
    {
        $regex = '/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*'.
                '(\.[A-z]{2,4})$/';

        if (!preg_match($regex, $email)) {
            $this->_currentValidationError = _('Must be a valid email');
            return false;
        }
        return true;
    }

    /**
     * 	validateDateTime
     * 	Validates a datetime value
     *
     * 	@param	string	$date.- Date to validate expected in yyyy-mm-dd hh:mm:ss
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validateDateTime($date)
    {
        list($date, $time) = explode(' ', $date);

        if (!self::validateDate($date) || !self::validateTime($time)) {
            $this->_currentValidationError = _('Must be a valid datetime');
            return false;
        }

        return true;
    }

    /**
     *  validateDate
     * 	Validates a date value
     *
     * 	@param	string	$date.- Date to validate expected in yyyy-mm-dd
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validateDate($date)
    {
        list($year, $month, $day) = explode('-', $date);
        $isValid = @checkdate($month, $day, $year);
        if (!$isValid) {
            $this->_currentValidationError = _('Must be a valid date');
            return false;
        }
        return true;
    }

    /**
     * 	validateTime
     * 	Validates a time value for mysql
     *
     * 	@param	string	$time.- Time to validate expected in hh:mm:ss
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validateTime($time)
    {
        list($hour, $minute, $second) = explode(':', $time);
        $isValid = true;

        $hour = (int)$hour;
        if (0 > $hour || 24 < $hour) {
            $isValid = false;
        }

        $minute = (int)$minute;
        if (0 > $minute || 59 < $minute) {
            $isValid = false;
        }

        $second = (int)$second;
        if (0 > $second || 59 < $second) {
            $isValid = false;
        }

        if (!$isValid) {
            $this->_currentValidationError = _('Must be a valid time');
            return false;
        }
        return true;
    }

    /**
     *  validateUrl
     * 	Validates an URL. This validation is part of smarty_validate plugin.
     *
     * 	@param	string	$url.- URL to validate
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validateUrl($url)
    {
        $isValid = preg_match('!^http(s)?://[\w-]+\.[\w-]+(\S+)?$!i', $url);
        if (!$isValid) {
            $this->_currentValidationError = _('Must be a valid URL');
            return false;
        }
        return true;
    }

    /**
     *  validatematchRegex
     * 	Validates that a string matches a regex
     *
     * 	@param	string	$regex.- Regex to match
     *	@param	string	$string.- String to validate
     *
     * 	@return	boolean	True if valid, false otherwise
     */
    private function validatematchRegex($regex, $string)
    {
        $isValid = preg_match($regex, $string);
        if (!$isValid) {
            $this->_currentValidationError = _('Value is not valid');
            return false;
        }
        return true;
    }
}
