<?php

include_once dirname(__FILE__).'/../services/Phone.php';
include_once dirname(__FILE__).'/Controller.php';

function to_proofs(PhoneProofDB $proof) {
    return $proof->url;
};

class MainController extends Controller {

    /**
     * @var BlacklistDB db
     */
    public $db;
    public $phone, $back_url;

    function index() {
        return $this->render("index", array("sites" => $this->db->getKnownSites(),
            "total_count" => $this->db->countPhones(),
            "non_reviewed" => $this->db->getNonReviewedPhonesCount()
        ));
    }

    private function render_phone($phone) {
        return $phone;
    }

    function search() {
        if (trim($this->phone) == "") {
            return $this->redirect(Site::index());
        }
        return $this->redirect(Site::a(Site::phone($this->phone)));
    }


    function ajax_check() {
        $pi = $this->db->findPhoneInfo(unsearch(urldecode($this->phone)));
        if ($pi) {
            $proofs = $pi->proofs();
            return array("found" => true, "count"=> count($proofs), "proofs" => array_map("to_proofs", $proofs));
        }
        else {
            return array("found" => false, "count" => 0);
        }
    }

    function phone() {
        $pi = $this->db->findPhoneInfo(unsearch(urldecode($this->phone)));
        if ($pi) {
            return $this->render("result", array("original" => urldecode($this->phone),
                "pi" => $pi,
                "result" => I18N::result(count($pi->proofs())),
                "phone" => $this->render_phone($pi->id)
            ), "layout");
        }
        else {
            return $this->renderNoResult(urldecode($this->phone));
        }
    }

    function redirect_if_found()  {
        $pi = $this->db->findPhoneInfo(unsearch($this->phone));
        if ($pi) {
            $proofs = $pi->proofs();
            return $this->redirect($proofs[0]->url);
        }
        else if ($this->back_url) {
            return $this->redirect($this->back_url);
        }
        else {
            return $this->search();
        }

    }

    public function renderNoResult($phone) {
        return $this->render("no-result", array("original" => $phone,
            "phone" => $this->render_phone($phone),
            "result" => I18N::no_result(),
            "sites" => $this->db->getKnownSites()
        ), "layout");
    }

    function full_list() {
        $list = $this->db->listPhones();
        return $this->render("list", array("phones" => $list));
    }



}
