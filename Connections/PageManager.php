<?php


namespace pages;

use \DB;
use \Tools;


class PageManager
{
    private $post, $session, $cookie, $db, $tools;

    public function __construct ($dbconn)
    {
        $this->post = $_POST;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->db = new \DB\DataBase($dbconn);
        $this->tools = new \Tools\Tools();
    }

    /**
     * Метод устновки свойств класса
     *
     * @param string $key название свойства
     * @param string $val значение свойства
     * @return mixed
     */
    public function set ( $key, $val )
    {
        $this->$key = $val;
    }

    public function createCat()
    {
        $parid = $this->post['parent'];
        $this->post['path'] = $this->tools->translit($this->post['path']);

        $parentfolder = $this->db->query("SELECT * FROM cat WHERE id='$parid'", 0, 'result', true);
        $parentfold = $this->db->fetch($parentfolder);
        if ($parentfold['path']) {
            $parentf = $parentfold['path'];
        } else {
            $parentf = "";
        }
        $newpath = $parentf . "/" . $this->post['path'];

        if (!$this->post['menu']) {
            $this->post['menu'] = "Y";
        }

        $insertSQL = sprintf("INSERT INTO cat (parent, name, `path`, menu, ptext, sort) VALUES (%s, %s, %s, %s, %s, %s)",
            $this->db->prepare($this->post['parent'], "int"),
            $this->db->prepare($this->post['name'], "text"),
            $this->db->prepare($newpath, "text"),
            $this->db->prepare($this->post['menu'], "text"),
            $this->db->prepare($this->post['ptext'], "text"),
            $this->db->prepare($this->post['sort'], "int"));

        $this->db->query($insertSQL, 0, 'result', true);

        //Определяем id новой записи
        $parentfolder = $this->db->query("SELECT * FROM cat WHERE path='$newpath'", 0, 'result', true);
        $parentfold = $this->db->fetch($parentfolder);
        $idnew = $parentfold['id'];


        $insertSQL = sprintf("INSERT INTO content (cat, `path`, text, caption, title, description, kod, template) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            $this->db->prepare($idnew, "int"),
            $this->db->prepare($newpath, "text"),
            $this->db->prepare($this->post['contenttext'], "text"),
            $this->db->prepare($this->post['name'], "text"),
            $this->db->prepare($this->post['name'], "text"),
            $this->db->prepare($this->post['text'], "text"),
            $this->db->prepare($this->post['kod'], "text"),
            $this->db->prepare($this->post['template'], "text"));

        $this->db->query($insertSQL, 0, 'result', true);

        return $idnew;
    }

    public function createCatFromArray($array){
        $createdCats = array();

        for($i = 0; $i < count($array); $i++){
            $this->set('post', $array[$i]);
            $createdCats[] = $this->createCat();
        }

        return $createdCats;
    }

    public function deleteCat($id)
    {
        $res = '';
        $id = intval($id);
        $p = $this->db->query("SELECT path, name FROM cat WHERE id='$id'", 0, 'row', true);
        $c = $this->db->query("SELECT id FROM cat WHERE parent='$id'", 0, 'result', true);
        $this->db->query("DELETE FROM cat WHERE id='$id'", 0, 'result', true);
        $this->db->query("DELETE FROM content WHERE cat='$id'", 0, 'result', true);
        if ($this->db->numrows($c) > 0) {
            $rc = $this->db->fetch($c);
            do {
                if (intval($rc['id']) > 0) $this->deleteCat($rc['id']);
            } while ($rc = $this->db->fetch($c));
        }
        el_log('Удален раздел &laquo;' . $p['name'] . '&raquo;', 1);
        el_clearcache('menu');
    }
}