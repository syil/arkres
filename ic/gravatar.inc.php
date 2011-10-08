<?php

/**
* Usage:
* <code>
*        $email = "youremail@yourhost.com";
*        $default = "http://www.yourhost.com/default_image.jpg";    // Optional
*        $gravatar = new Gravatar($email, $default);
*        $gravatar->size = 80;
*        $gravatar->rating = "G";
*        $gravatar->border = "FF0000";
*
*        echo $gravatar; // Or echo $gravatar->toHTML();
* </code>
*
*    Class Page: http://www.phpclasses.org/browse/package/4227.html
*
* @author Lucas Araújo <araujo.lucas@gmail.com>
* @version 1.0
* @package Gravatar
*/
class Gravatar
{
    const GRAVATAR_URL = "http://www.gravatar.com/avatar.php";
	
    private $GRAVATAR_RATING = array("G", "PG", "R", "X");
    
    protected $properties = array(
        "gravatar_id"    => NULL,
        "default"        => NULL,
        "size"           => 80,        // The default value
        "rating"         => NULL,
        "border"         => NULL,
    );
    protected $email = "";
    protected $extra = "";
	
    public function Gravatar($email=NULL, $default=NULL) {
        $this->setEmail($email);
        $this->setDefault($default);
    }
	
    public function setEmail($email) {
        if ($this->isValidEmail($email)) {
            $this->email = $email;
            $this->properties['gravatar_id'] = md5(strtolower($this->email));
            return true;
        }
        return false;
    }
    public function setDefault($default) {
        $this->properties['default'] = $default;
    }

    public function setRating($rating) {
        if (in_array($rating, $this->GRAVATAR_RATING)) {
            $this->properties['rating'] = $rating;
            return true;
        }
        return false;
    }

    public function setSize($size) {
        $size = (int) $size;
        if ($size <= 0)
            $size = NULL;        // Use the default size
        $this->properties['size'] = $size;
    }

    public function setExtra($extra) {
        $this->extra = $extra;
    }

    public function isValidEmail($email) {
        // Source: http://www.zend.com/zend/spotlight/ev12apr.php
        return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
    }

    public function __get($var) { return @$this->properties[$var]; }
    public function __set($var, $value) {
        switch($var) {
            case "email":    return $this->setEmail($value);
            case "rating":    return $this->setRating($value);
            case "default":    return $this->setDefault($value);
            case "size":    return $this->setSize($value);
            // Cannot set gravatar_id
            case "gravatar_id": return;
        }
        return @$this->properties[$var] = $value;
    }

    public function __isset($var) { return isset($this->properties[$var]); }

    public function __unset($var) { return @$this->properties[$var] == NULL; }

    public function getSrc() {
        $url = self::GRAVATAR_URL ."?";
        $first = true;
        foreach($this->properties as $key => $value) {
            if (isset($value)) {
                if (!$first)
                    $url .= "&";
                $url .= $key."=".urlencode($value);
                $first = false;
            }
        }
        return $url;    
    }

    public function toHTML() {
        return     '<img src="'. $this->getSrc() .'"'
                .(!isset($this->size) ? "" : ' style="width:'.$this->size.'px; height:'.$this->size.'px')
                .$this->extra
                .' />';    
    }
    public function __toString() { return $this->toHTML(); }
}

?> 