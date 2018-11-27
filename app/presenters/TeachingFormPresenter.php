<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:32
 */

namespace App\Presenters;


use App\Model\TeachingFormModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class TeachingFormPresenter extends BasePresenter {


    private $teachingFormModel;

    /**
     * TeachingFormPresenter constructor.
     * @param $teachingFormModel
     */
    public function __construct(TeachingFormModel $teachingFormModel)
    {
        parent::__construct();
        $this->teachingFormModel = $teachingFormModel;
    }

    /**
     * Create edit teaching form form.
     * @return Form Edit teaching form form
     */
    protected function createComponentEditTeachingFormForm(): Form {
        $teachingFormId = $this->getParameter('id');
        $teachingForm = isset($teachingFormId) ? $this->teachingFormModel->getById($teachingFormId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teachingForm ? $teachingForm['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $teachingForm ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->teachingForms = $this->teachingFormModel->getAll();
        $this->template->tabs = [
            'Způsoby zakončení' => 'CompletionType:',
            'Kategorie' => 'Category:',
            'Způsoby výuky' => 'CourseType:',
            'Předměty' => 'Subject:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add teaching form form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if(empty($this->getParameter('id'))) {
                $this->teachingFormModel->insert($form->getValues(true));
                $this->flashMessage('Forma výuky byla přidána.', self::$SUCCESS);
            } else {
                $this->teachingFormModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Forma výuky byla upravena.', self::$SUCCESS);
            }

            $this->redirect('TeachingForm:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete teaching form by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->teachingFormModel->deleteById($id);
            $this->flashMessage('Forma výuky byla vymazána.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('TeachingForm:');
    }

}