<?php

class PhoneInfoDB {
    public $id;
    public $owner_name;
    public $owner_url;
    public $marked_as_good;
    public $reviewed;
    public $proof_of_good;
    public $victims_count;
    public $original_phone;

    private $_proofs = array();
    private $_aliases = array();

    public function add_original_phone($phone) {
        $this->original_phone = normalize_phone($phone, true);
    }

    public function add_proof($proof) {
        $this->_proofs[] = $proof;
    }

    public function proofs() {
        return $this->_proofs;
    }

    public function get_phone() {
        return $this->id;
    }

    public function has_proofs() {
        return count($this->_proofs) > 0;
    }

    public function add_alias($alias) {
        $this->_aliases[] = $alias;
    }

    public function has_alias($phone) {
        return in_array($phone, $this->_aliases);
    }
}
