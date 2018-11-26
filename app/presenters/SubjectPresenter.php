<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 20:21
 */

namespace App\Presenters;


use App\Model\CompletionTypeModel;
use App\Model\SubjectModel;
use App\Model\TeachingFormModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

final class SubjectPresenter extends BasePresenter {

    private $subjectModel;
    private $teachingFormModel;
    private $completionTypeModel;

    /**
     * SubjectPresenter constructor.
     * @param $subjectModel
     * @param $teachingFormModel
     * @param $completionTypeModel
     */
    public function __construct(SubjectModel $subjectModel, TeachingFormModel $teachingFormModel, CompletionTypeModel $completionTypeModel) {
        parent::__construct();
        $this->subjectModel = $subjectModel;
        $this->teachingFormModel = $teachingFormModel;
        $this->completionTypeModel = $completionTypeModel;
    }


    /**
     * Create edit subject form.
     * @return Form Edit subject form
     */
    protected function createComponentEditSubjectForm(): Form {
        $subjectId = $this->getParameter('id');
        $subject = isset($subjectId) ? $this->subjectModel->getById($subjectId) : null;
        $teachingForms = $this->teachingFormModel->getAll();
        $completionType = $this->completionTypeModel->getAll();

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($subject ? $subject['nazev'] : '')
            ->setMaxLength(255);

        $form->addText('shortName', 'Zkratka')
            ->setRequired('Prosím vyplňte zkratku.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($subject ? $subject['zkratka'] : '')
            ->setMaxLength(10);

        $form->addSelect('teachingForm', 'Forma výuky', array_reduce($teachingForms, function ($result, $teachingForm) {
            $result[$teachingForm['id']] = $teachingForm['nazev'];
            return $result;
        }))
            ->setDefaultValue($subject['forma_vyuky_id'])
            ->setRequired("Prosím vyplňte formu výuky");

        $form->addSelect('completionType', 'Způsob zakončení', array_reduce($completionType, function ($result, $completionType) {
            $result[$completionType['id']] =  $completionType['nazev'];
            return $result;
        }))
            ->setDefaultValue($subject['zpusob_zakonceni_id'])
            ->setRequired("Prosím vyplňte způsob zakončení");

        $form->addSubmit('send', $subject ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->subjects = $this->subjectModel->getAll();
        $this->template->tabs = [
            'Způsoby zakončení' => 'CompletionType:',
            'Formy výuky' => 'TeachingForm:',
            'Kategorie' => 'Category:',
            'Způsoby výuky' => 'CourseType:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add subject form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->subjectModel->insert($form->getValues(true));
                $this->flashMessage('Předmět byl přidán.', self::$SUCCESS);
            } else {
                $this->subjectModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Předmět byl upraven.', self::$SUCCESS);
            }

            $this->redirect('Subject:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete subject by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->subjectModel->deleteById($id);
            $this->flashMessage('Předmět byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Subject:');
    }
}