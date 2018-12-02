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

        $form->addText('shortName', 'Zkratka')
            ->setRequired('Prosím vyplňte název pro zkratku.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('zkratka')
            ->setMaxLength(50);

        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název pro nazev.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('nazev')
            ->setMaxLength(50);

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