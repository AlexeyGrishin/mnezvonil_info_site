<?php

include_once dirname(__FILE__).'/../services/Phone.php';
include_once dirname(__FILE__).'/../view/admin/adminhelper.php';

class AdminController extends Controller{
    /**
     * @var BlacklistDB $db;
     */
    public $db;
    /**
     * @var Auth @auth;
     */
    public $auth;
    public $name, $password, $post, $phone, $proof, $site_id, $resolution, $proof_of_good;


    function login() {
        return $this->render("admin/admin-login", array("error" => null));
    }

    function before($action) {
        if ($action != "login" && $action != "doLogin") {
            $manager = $this->checkRole("manager");
            if ($manager) {
                return null;
            }
            else {
                if (strpos($action, "ajax_") === 0) {
                    throw new Exception(I18N::insufficientRights());
                }
                else {
                    return 1;
                }
            }
        }
        return null;
    }

    function logout() {
        $this->auth->logout();
        return $this->redirect(Site::index());
    }

    function doLogin() {
        echo $this->name;
        if ($this->auth->login($this->name, $this->password)) {
            return $this->redirect(Site::a(Site::$admin_index));
        }
        else {
            return $this->render("admin/admin-login", array("error" => I18N::loginError()));
        }
    }

    private function checkRole($role) {
        if ($this->auth->is_authenticated_as($role)) {
            return true;
        }
        else {
            //echo Site::$login;
            $this->redirect(Site::a(Site::login()));
            return false;
        }
    }

    function logs() {
        return $this->render("admin/admin-logs", array(
            "logs" => $this->db->listLogs($this->site_id, 100),
            "site_name" => $this->db->getSiteName($this->site_id)
        ));
    }

    function index() {
        $sites = $this->db->getKnownSites();
        $logs = array();
        foreach ($sites as $site) {
            $log = $this->db->getLastLog($site->id);
            if ($log) {
                $logs[] = array($site->domain, $log, $site->id);
            }
        }
        return $this->render("admin/admin-main",
            array(
                "phones_count" => $this->db->getNonReviewedPhonesCount(),
                "phones_without_proofs" => $this->db->getPhonesCountWithoutProofs(),
                "logs" => $logs,
                "sites" => $sites
            ));
    }

    function delete_without_proofs() {
        $this->db->removePhonesWithoutProofs();
        return 1;
    }

    function phones() {
        $max = 31;
        $proofs_per_phone = $this->db->listNonReviewedPhones($max, $this->site_id);
        if (count($proofs_per_phone) == $max) {
            $has_more = true;
            array_pop($proofs_per_phone);
        }
        else {
            $has_more = false;
        }
        return $this->render("admin/admin-phones", array("phones" => $proofs_per_phone, "has_more" => $has_more));
    }

    function phone() {
        $phone_info = $this->db->findPhoneInfo(unsearch($this->phone), true);

        return $this->render("admin/admin-phone", array("phone" => $phone_info));
    }

    function save_phone() {
        switch ($this->resolution) {
            case "good":
                if ($this->proof_of_good == "") {
                    error(I18N::proof_is_empty());
                    return $this->redirect(Site::admin_phone($this->phone));
                }
                $this->db->markAsReviewed($this->phone, $this->proof_of_good);
                break;
            case "bad":
                $this->db->markAsReviewed($this->phone);
                break;
            default:
                $this->db->markAsPostponed($this->phone);
        }
        notice(I18N::phone_saved($this->resolution));
        return $this->redirect(Site::admin_phone($this->phone));
    }

    function ajax_approve() {
        try {
            $this->db->markAsReviewed($this->phone, $this->proof);
            return 1;
        }
        catch (PhoneNotFoundException $e) {
            throw new Exception(I18N::phoneNotFound($e->phone));
        }

    }

    function ajax_approve_post() {
        $this->db->markAllAsReviewed($this->post);
        return 1;
    }

    function ajax_approve_proof() {
        $this->db->markProofAs($this->proof, 0);
        return 1;
    }

    function ajax_reject_proof() {
        $this->db->markProofAs($this->proof, 1);
        return 1;
    }


}
