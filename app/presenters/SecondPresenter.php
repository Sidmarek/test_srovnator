<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

/**
 * Description of SecondPresenter
 *
 * @author Marek
 */
class SecondPresenter extends BasePresenter {
    public function renderDefault()
	{
	}
    protected function createComponentAditionalForm() { //Vytvoření formuláře
        $form = new Nette\Application\UI\Form;
        $this->session->start();
        $section = $this->getSession('udaje');// získání údajů ze session
        if ($section->vozidlo=='auto' || $section->vozidlo=='motorka') {     //Podmínka pro typ vozidla
            $form->addText('objem_vozidla', 'Objem vozidla (v ccm):')
                    ->setType('number')
                    ->addRule(Form::INTEGER, "Zadejte platný objem vozidla.")
                    ->addRule(Form::MAX_LENGTH, 'Objem motoru nesmí být více než 9 999 ccm', 4); //Validace délky

            $form->addText('vykon_vozidla', 'Výkon vozidla (v kW):')
                    ->setType('number')
                    ->addRule(Form::INTEGER, "Zadejte platný výkon vozidla.")
                    ->addRule(Form::MAX_LENGTH, 'Výkon motoru nesmí být 9 999 kW', 4);
        }
        else {
            $form->addText('hmotnost', 'Hmotnost vozidla (v kg):')
                    ->setType('number')
                    ->addRule(Form::INTEGER, "Zadejte platnou hmotnost vozidla.")
                    ->addRule(Form::MAX_LENGTH, 'Hmotnost vozidla nesmí být více než 99 999 kg', 5);
        }
        
        $form->addSubmit('send', 'Vypočítat cenu');
        $form->onSuccess[] = array($this, 'aditionalFormSucceeded');
            
        return $form;
    }
    
    public function AditionalFormSucceeded ($form, $values) { //metoda pro zpracování dat z formuláře a vložení do session
        $this->session->start();
        $section = $this->getSession('udaje'); // získání údajů ze session
        $section->setExpiration('2 minutes');
        if ($section->vozidlo=='auto' || $section->vozidlo=='motorka') { //zápis buď údajů o objemu či o výkonu do session závisle na typu auta
            $section->objem_vozidla = $values->objem_vozidla;
            $section->vykon_vozidla = $values->vykon_vozidla;
        }
        else {
            $section->hmotnost = $values->hmotnost;
        }
        $this->flashMessage('Děkuji za vaše údaje', 'success');
        $this->redirect('Overview:show');
    }
}
