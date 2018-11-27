<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 23:37
 */

namespace App\Presenters;


use App\Model\RoleModel;
use App\Model\SubjectInFieldModel;
use App\Model\TeacherModel;
use App\Model\TeachingModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class TeachingPresenter extends BasePresenter {

    private $teachingModel;

    private $teacherModel;

    private $roleModel;

    private $subjectInFieldModel;

    /**
     * TeachingPresenter constructor.
     * @param $teachingModel
     * @param $teacherModel
     * @param $roleModel
     * @param $subjectInFieldModel
     */
    public function __construct(TeachingModel $teachingModel, TeacherModel $teacherModel, RoleModel $roleModel,
                                SubjectInFieldModel $subjectInFieldModel) {
        parent::__construct();
        $this->teachingModel = $teachingModel;
        $this->teacherModel = $teacherModel;
        $this->roleModel = $roleModel;
        $this->subjectInFieldModel = $subjectInFieldModel;
    }

    /**
     * Create edit teaching form.
     * @return Form Edit teaching form
     */
    protected function createComponentEditTeachingForm(): Form {
        $teachingId = $this->getParameter('id');
        $teaching = isset($teachingId) ? $this->teachingModel->getById($teachingId) : null;
        $teachers = $this->teacherModel->getAll();
        $subjectsInField = $this->subjectInFieldModel->getAll();
        $roles = $this->roleModel->getAll();

        $form = new Form;

        $form->addSelect('subjectInField', 'Předmět v oboru', array_reduce($subjectsInField, function ($result, $subjectInField) {
            $result[$subjectInField['id']] = $subjectInField['obor'] . ' - ' . $subjectInField['predmet'];
            return $result;
        }))
            ->setDefaultValue($teaching['predm_obor_id'])
            ->setRequired("Prosím vyplňte předmět v oboru");

        $form->addSelect('teacher', 'Vyučující', array_reduce($teachers, function ($result, $teacher) {
            $result[$teacher['id']] = $teacher['jmeno'] . ' ' . $teacher['prijmeni'];
            return $result;
        }))
            ->setDefaultValue($teaching['ucitel_id'])
            ->setRequired("Prosím vyplňte vyučujícího");

        $form->addSelect('role', 'Role', array_reduce($roles, function ($result, $role) {
            $result[$role['id']] = $role['nazev'];
            return $result;
        }))
            ->setDefaultValue($teaching['role_id'])
            ->setRequired("Prosím vyplňte roli");

        $form->addSubmit('send', $teaching ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->teachings = $this->teachingModel->getAll();
        $this->template->tabs = [
            'Předměty v oboru' => 'SubjectInField:',
            'Způsoby výuky předmětu' => 'CourseTypeInField:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add teaching form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if(empty($this->getParameter('id'))) {
                $this->teachingModel->insert($form->getValues(true));
                $this->flashMessage('Učení předmětu bylo přidáno.', self::$SUCCESS);
            } else {
                $this->teachingModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Učení předmětu bylo upraveno.', self::$SUCCESS);
            }

            $this->redirect('Teaching:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete teaching by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->teachingModel->deleteById($id);
            $this->flashMessage('Učení předmětu bylo vymazáno.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Teaching:');
    }

}