<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-02
 * Time: 18:20
 */

namespace App\Presenters;


use App\Model\ImportModel;
use Nette\Application\UI\Form;

class ImportDepartmentPresenter extends BasePresenter {

    private $importModel;

    /**
     * ImportDepartmentPresenter constructor.
     * @param $importModel
     */
    public function __construct(ImportModel $importModel) {
        parent::__construct();
        $this->importModel = $importModel;
    }

    /**
     * Create import form.
     * @return Form
     */
    function createComponentImportSubjectsForm() {
        $form = new Form;

        $form->addUpload('file', 'Importovaný soubor (JSON)')
            ->setRequired(true);


        $form->addSubmit('send', 'Importovat');

        return $form;
    }

    function renderDefault(){
        $this->requireAdmin();
        $this->template->tabs = [
            'Import učitelů' => 'ImportTeacher:',
            'Import předmětů' => 'ImportSubject:',
        ];
    }
}