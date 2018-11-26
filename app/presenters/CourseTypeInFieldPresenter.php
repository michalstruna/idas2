<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 23:12
 */

namespace App\Presenters;


use App\Model\CourseTypeInFieldModel;
use App\Model\CourseTypeModel;
use App\Model\SubjectInFieldModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class CourseTypeInFieldPresenter extends BasePresenter {

    private $courseTypeInFieldModel;

    private $subjectInFieldModel;

    private $courseTypeModel;

    /**
     * CourseTypeInFieldPresenter constructor.
     * @param $courseTypeInFieldModel
     * @param $subjectInFieldModel
     * @param $courseTypeModel
     */
    public function __construct(CourseTypeInFieldModel $courseTypeInFieldModel, SubjectInFieldModel $subjectInFieldModel,
                                CourseTypeModel $courseTypeModel) {
        parent::__construct();
        $this->courseTypeInFieldModel = $courseTypeInFieldModel;
        $this->subjectInFieldModel = $subjectInFieldModel;
        $this->courseTypeModel = $courseTypeModel;
    }

    /**
     * Create edit course type in field form.
     * @return Form Edit course type in field form
     */
    protected function createComponentEditCourseTypeInFieldForm(): Form {
        $courseTypeInFieldId = $this->getParameter('id');
        $courseTypeInField = isset($courseTypeInFieldId) ? $this->courseTypeInFieldModel->getById($courseTypeInFieldId) : null;
        $subjectsInFields = $this->subjectInFieldModel->getAll();
        $courseTypes = $this->courseTypeModel->getAll();

        $form = new Form;

        $form->addSelect('subjectInField', 'Předmět v oboru', array_reduce($subjectsInFields, function ($result, $subjectInField) {
            $result[$subjectInField['id']] = $subjectInField['obor'] . ' - ' . $subjectInField['predmet'];
            return $result;
        }))
            ->setDefaultValue($courseTypeInField['predm_obor_id'])
            ->setRequired("Prosím vyplňte předmět v oboru");

        $form->addSelect('courseType', 'Způsob výuky', array_reduce($courseTypes, function ($result, $courseType) {
            $result[$courseType['id']] = $courseType['nazev'];
            return $result;
        }))
            ->setDefaultValue($courseTypeInField['zpusob_vyuky_id'])
            ->setRequired("Prosím vyplňte způsob výuky");

        $form->addInteger('hours', 'Počet hodin')
            ->setRequired('Prosím vyplňte počet hodin.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($courseTypeInField ? $courseTypeInField['pocet_hodin'] : '')
            ->setMaxLength(10);

        $form->addInteger('capacity', 'Kapacita')
            ->setRequired('Prosím vyplňte kapacitu.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($courseTypeInField ? $courseTypeInField['kapacita'] : '');

        $form->addSubmit('send', $courseTypeInField ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->courseTypesInField = $this->courseTypeInFieldModel->getAll();
        $this->template->tabs = [
            'Předměty v oboru' => 'SubjectInField:',
            'Učí' => 'Teaching:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add course type in field form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->courseTypeInFieldModel->insert($form->getValues(true));
                $this->flashMessage('Způsob výuky předmětu byl přidán.', self::$SUCCESS);
            } else {
                $this->courseTypeInFieldModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Způsob výuky předmětu byl upraven.', self::$SUCCESS);
            }

            $this->redirect('CourseTypeInField:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete course type in field by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->courseTypeInFieldModel->deleteById($id);
            $this->flashMessage('Způsob výuky předmětu byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('CourseTypeInField:');
    }

}