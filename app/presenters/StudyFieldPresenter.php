<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 21:46
 */

namespace App\Presenters;


use App\Model\StudyFieldModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class StudyFieldPresenter extends BasePresenter {

    private $studyFieldModel;

    /**
     * StudyFieldPresenter constructor.
     * @param $studyFieldModel
     */
    public function __construct(StudyFieldModel $studyFieldModel)
    {
        parent::__construct();
        $this->studyFieldModel = $studyFieldModel;
    }

    /**
     * Create edit study field form.
     * @return Form Edit completion type form
     */
    protected function createComponentEditStudyFieldForm(): Form {
        $studyFieldId = $this->getParameter('id');
        $studyField = isset($studyFieldId) ? $this->studyFieldModel->getById($studyFieldId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($studyField ? $studyField['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $studyField ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->studyFields = $this->studyFieldModel->getAll();
        $this->template->tabs = [];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add study field form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if(empty($this->getParameter('id'))) {
                $this->studyFieldModel->insert($form->getValues(true));
                $this->flashMessage('Obor byl přidán.', self::$SUCCESS);
            } else {
                $this->studyFieldModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Obor byl upraven.', self::$SUCCESS);
            }

            $this->redirect('StudyField:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete study field by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->studyFieldModel->deleteById($id);
            $this->flashMessage('Obor byl vymazán.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('StudyField:');
    }

}