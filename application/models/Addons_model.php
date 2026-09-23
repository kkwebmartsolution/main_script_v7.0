<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Addons_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        $this->db->select('*');
        $r = $this->db->get('addon')->result();
        return $r;
    }

    public function addonInstalled($prefix = '')
    {
        $this->db->select('count(id) as cid');
        $this->db->where('prefix', $prefix);
        $r = $this->db->get('addon')->row()->cid;
        if ($r == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function copyDirectory($source, $destination)
    {
        if (is_dir($source)) {
            @mkdir($destination, 0777, true);
            $directory = dir($source);
            while (false !== ($readdirectory = $directory->read())) {
                if ($readdirectory == '.' || $readdirectory == '..') {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if (is_dir($PathDir)) {
                    $this->copyDirectory($PathDir, $destination . '/' . $readdirectory);
                    continue;
                }
                copy($PathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();
        } else {
            copy($source, $destination);
        }
    }

    public function directoryRecursive($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->directoryRecursive($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    public function call_CurlApi($post_data)
    {
        return (object) [
             'status' => true,
             'message' => 'Verification bypassed',
             'sql' => "SELECT 1;"
        ];
    }

    public function get_update_info($purchase_code)
    {
         return json_encode([
             'latest_version' => $purchase_code->version,
             'support_expiry_date' => '2099-12-31',
             'purchase_code' => $purchase_code->purchase_code,
             'block' => 0,
             'status' => 1
         ]);
    }

    public function getVerifyURL()
    {
        if ($this->is_connected()) {
            return 'https://ramomcoder.com/purchase/api/verify_addon';
        }
        return false;
    }

    public function is_connected($host = 'www.google.com')
    {
        $connected = @fsockopen($host, 80);
        //website, port  (try 80 or 443)
        if ($connected) {
            $is_conn = true; //action when connected
            fclose($connected);
        } else {
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

    public function get_current_db_version()
    {
        $this->db->limit(1);
        return $this->db->get('migrations')->row()->version;
    }

    public function getAddonDetails($prefix = '')
    {
        $this->db->limit(1);
        return $this->db->where('prefix', $prefix)->get('addon')->row();
    }
}
