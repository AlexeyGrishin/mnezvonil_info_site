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

    function distribute($pi, Cities $cities) {
        $ret = array();
        if (is_cell($pi->original_phone)) {
            $ret[] = array("scope" => false, "info" => $pi->proofs(), "phone" => $pi->id, "code" => false);
        }
        else if (is_full($pi->original_phone)) {
            $city_phone = get_local_phone($pi->id);
            $city_code = get_city_code($pi->id);
            $city = $cities->perCode($city_code);
            $ret[] = array("scope" => $city->title, "info" => $pi->proofs(), "code" => $city_code, "phone" => $city_phone);
        }
        else {
            $city_phone = get_local_phone($pi->id);
            $per_city = array();
            $proofs = $pi->proofs();
            foreach ($proofs as $proof) {
                $city_code = get_city_code($proof->phone_id);
                $city = $cities->perCode($city_code);
                if (!array_key_exists($city->title, $per_city)) {
                    $per_city[$city->title] = array(
                        "scope" => $city->title,
                        "phone" => $city_phone,
                        "code" => $city_code,
                        "info" => array()
                    );
                }
                $per_city[$city->title]["info"][] = $proof;
            }
            $all_cities = $cities->all();
            $NOTHING = "n/a";
            foreach ($all_cities as $city) {
                if (!array_key_exists($city->title, $per_city)) {
                    $ret[] = array(
                        "scope" => $city->title,
                        "phone" => $city_phone,
                        "code" => $city->phone_code,
                        "info" => array()
                    );
                    $per_city[$city->title] = $NOTHING;
                }
            }
            foreach ($per_city as $city => $result) {
                if ($result != $NOTHING) {
                    $ret[] = $result;
                }
            }
        }
        foreach ($ret as $key => $info) {
            $ret[$key]["result"] = I18N::result(count($info["info"]));
        }
        return $ret;
    }

    private function assignCities($pi, Cities $cities) {
        $proofs = $pi->proofs();
        foreach ($proofs as $proof) {
            $proof->init_city_name($cities);
        }
    }

    private function assignSites($pi, $sites) {
        $proofs = $pi->proofs();
        foreach ($proofs as $proof) {
            foreach ($sites as $site) {
                if ($site->id == $proof->known_site_id) {
                    $proof->site_name = $site->domain;
                    break;
                }
            }
        }
    }

    function phone() {
        $pi = $this->db->findPhoneInfo(unsearch(urldecode($this->phone)));
        if ($pi) {
            $pi->add_original_phone(urldecode($this->phone));
            $cities = $this->db->getCities();
            $sites = $this->db->getKnownSitesEvenInactive();
            $this->assignCities($pi, $cities);
            $this->assignSites($pi, $sites);
            return $this->render("result", array(
                "original" => urldecode($this->phone),
                "pi" => $pi,
                "result" => I18N::result(count($pi->proofs())),
                "phone" => $this->render_phone($pi->original_phone),
                "grouped" => $this->distribute($pi, $cities)
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
