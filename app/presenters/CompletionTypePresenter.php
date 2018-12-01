<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:19
 */

namespace App\Presenters;


use App\Model\CompletionTypeModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class CompletionTypePresenter extends BasePresenter {

    private $completionTypeModel;

    /**
     * CompletionTypePresenter constructor.
     * @param $completionTypeModel
     */
    public function __construct(CompletionTypeModel $completionTypeModel) {
        parent::__construct();
        $this->completionTypeModel = $completionTypeModel;
    }

    /**
     * Create edit completion type form.
     * @return Form Edit completion type form
     */
    protected function createComponentEditCompletionTypeForm(): Form {
        $completionTypeId = $this->getParameter('id');
        $completionType = isset($completionTypeId) ? $this->completionTypeModel->getById($completionTypeId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($completionType ? $completionType['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $completionType ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->completionTypes = $this->completionTypeModel->getAll();
        $this->template->tabs = [
            'Formy výuky' => 'TeachingForm:',
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
     * Handler for edit or add completion type form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->completionTypeModel->insert($form->getValues(true));
                $this->flashMessage('Způsob zakončení byl přidán.', self::$SUCCESS);
            } else {
                $this->completionTypeModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Způsob zakončení byl upraven.', self::$SUCCESS);
            }

            $this->redirect('CompletionType:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete completion type by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->completionTypeModel->deleteById($id);
            $this->flashMessage('Způsob zakončení byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('CompletionType:');
    }

}