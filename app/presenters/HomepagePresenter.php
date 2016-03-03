<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
/**
 * Popis SecondPresenter
 * Slouží vytvoření formuláře, uložení dat do session a 
 * @author Marek
 */
class HomepagePresenter extends BasePresenter {

    private $database;
    
    
    public function __construct(Nette\Database\Context $database) 
    {
        $this->database = $database;
    }

        /*
         * Metoda pro vytvoření a validaci prvního ze dvou formulářů () pro výpočet ceny pojistného
         */
        
        protected function createComponentHomepageForm() {
            $form = new Nette\Application\UI\Form;

            $form->addText('psc', 'PSČ:')->addRule(Form::PATTERN, 'PSČ musí mít 5 číslic', '([0-9]\s*){5}'); //Pravidlo určující délku právě pěti číslic
            
            $form->addText('email', 'Email:')
                    ->addRule(Form::EMAIL, "Zadejte platnou emailovou adresu.") //Validace tvaru emailu
                    ->addRule(Form::MAX_LENGTH, 'Délka nesmí být více než 120 znaků',120); //Kontrola délky emailu
            
            $form->addText('vek', 'Věk:')
                   ->addRule(Form::INTEGER, 'Věk musí být číslo') //Validace zda se jedná o číslo
                   ->addRule(Form::RANGE, 'Věk musí být než 18 let', array(18, 120)); //Validace pro starší 18 let
            
            $vozidla = array( 
                'auto' => 'Osobní automobil',
                'motorka' => 'Motocykl',
                'suv' => 'Užitkový automobil',
                'van' => 'Nákladní automobil',
                'vozejk' => 'Přípojné vozidlo',
                'traktor' => 'Traktor',
                'karavan' => 'Obytný automobil',
                'stroj' => 'Pracovní stroj',
                'tahac' => 'Tahač',
                'autobus' => 'Autobus',
                'sanitka' => 'Sanitka',
            );
            
            $form->addSelect('vozidla', 'Typ vozidla:', $vozidla)->setPrompt('Zvolte typ vozidla');

            $form->addSubmit('send', 'Přejít na doplňující informace');

            $form->onSuccess[] = array($this, 'HomepageFormSucceeded');

            return $form;
        }
        
        public function HomepageFormSucceeded ($form, $values) { //metoda pro zpracování dat z formuláře a vložení do session
            $this->session->start();
            $section = $this->getSession('udaje'); //Načtení session
            $section->setExpiration('2 minutes');
            $section->psc = $values->psc;
            $section->email = $values->email;
            $section->vek = $values->vek;
            $section->vozidlo = $values->vozidla;
        $this->flashMessage('Děkuji za vaše údaje '.$section->vozidla, 'success');
        $this->redirect('Second:create');
    }
}
