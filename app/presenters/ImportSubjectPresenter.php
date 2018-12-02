<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-02
 * Time: 18:19
 */

namespace App\Presenters;


use App\Model\ImportModel;
use Nette\Application\UI\Form;

class ImportSubjectPresenter extends BasePresenter {

    private $importModel;

    /**
     * ImportSubjectPresenter constructor.
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
            'Import kateder' => 'ImportDepartment:'
        ];
    }
}