<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:31
 */

namespace App\Presenters;


use App\Model\CourseTypeModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class CourseTypePresenter extends BasePresenter {

    private $courseTypeModel;

    /**
     * CourseTypePresenter constructor.
     * @param $courseTypeModel
     */
    public function __construct(CourseTypeModel $courseTypeModel)
    {
        parent::__construct();
        $this->courseTypeModel = $courseTypeModel;
    }

    /**
     * Create edit course type form.
     * @return Form Edit completion type form
     */
    protected function createComponentEditCourseTypeForm(): Form {
        $courseTypeId = $this->getParameter('id');
        $courseType = isset($courseTypeId) ? $this->courseTypeModel->getById($courseTypeId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($courseType ? $courseType['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $courseType ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->courseTypes = $this->courseTypeModel->getAll();
        $this->template->tabs = [
            'Způsoby zakončení' => 'CompletionType:',
            'Formy výuky' => 'TeachingForm:',
            'Kategorie' => 'Category:',
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
     * Handler for edit or add course type form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if(empty($this->getParameter('id'))) {
                $this->courseTypeModel->insert($form->getValues(true));
                $this->flashMessage('Způsob výuky byl přidán.', self::$SUCCESS);
            } else {
                $this->courseTypeModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Způsob výuky byl upraven.', self::$SUCCESS);
            }

            $this->redirect('CourseType:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete course type by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->courseTypeModel->deleteById($id);
            $this->flashMessage('Způsob výuky byl vymazán.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('CourseType:');
    }

}