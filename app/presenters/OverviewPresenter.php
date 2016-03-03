<?php
namespace App\Presenters;

class OverviewPresenter extends BasePresenter {
    
  private $database;
    
    public function __construct(\Nette\Database\Context $database) {
        $this->database = $database; // Navázání komunikace s DB 
    }
    
    public function renderShow() { 
      $this->session->start();
        $section = $this->getSession('udaje');//Získání údajů ze session
        $this->template->udaje = $section;
        
        $pocatecni_cena = 1000;
        
        $cena = $pocatecni_cena;
        
        if (strpos($section->psc, "1") === true) { // Podmínky pro slevy a příplatky dle psc, věku, atd...
            $cena = $cena+($pocatecni_cena*0.1);
        }
        if ($section->vek < 26) {
            $cena = $cena+($pocatecni_cena*0.2);
        }
        if ($section->vek > 35) {
            $cena = $cena-($pocatecni_cena*0.1);
        }
        if ($section->objem_vozidla > 1600 || $section->vykon_vozidla > 100) {
            $cena = $cena+($pocatecni_cena*0.3);
        }
        if ($section->hmotnost > 3500) {
            $cena = $cena+($pocatecni_cena*0.2);
        }
        
        $this->template->cena = $cena;
        
        $this->database->table('zaznamy')->insert(array( //Vložení údajů do DB
            'psc' => $section->psc,
            'email' => $section->email,
            'vek' => $section->vek,
            'vozidlo' => $section->vozidlo,
            'cena' => $cena,
        ));
        if ($section->vozidlo=='auto' || $section->vozidlo=='motorka') { //Podmínka pro použití objem či výkonu
            $this->database->table('zaznamy')->insert(array(
                'objem' => $section->objem_vozidla,
                'vykon' => $section->vykon_vozidla, 
            ));
        } else {
            $this->database->table('zaznamy')->insert(array(
                'hmotnost' => $section->hmotnost,
            ));
        }  
        
    }    
}
