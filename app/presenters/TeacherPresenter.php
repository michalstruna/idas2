<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 21:22
 */

namespace App\Presenters;


use App\Model\DepartmentModel;
use App\Model\TeacherModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class TeacherPresenter extends BasePresenter {

    private $teacherModel;

    private $departmentModel;

    /**
     * TeacherPresenter constructor.
     * @param TeacherModel $teacherModel
     * @param DepartmentModel $departmentModel
     */
    public function __construct(TeacherModel $teacherModel, DepartmentModel $departmentModel) {
        parent::__construct();
        $this->teacherModel = $teacherModel;
        $this->departmentModel = $departmentModel;
    }

    /**
     * Create edit teacher form.
     * @return Form Edit teacher form
     */
    protected function createComponentEditTeacherForm(): Form {
        $teacherId = $this->getParameter('id');
        $teacher = isset($teacherId) ? $this->teacherModel->getById($teacherId) : null;

        $departments = $this->departmentModel->getAll();

        $form = new Form;
        $form->addText('firstName', 'Jméno')
            ->setRequired('Prosím vyplňte jméno.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teacher ? $teacher['jmeno'] : '')
            ->setMaxLength(80);

        $form->addText('lastName', 'Příjmení')
            ->setRequired('Prosím vyplňte příjmení.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teacher ? $teacher['prijmeni'] : '')
            ->setMaxLength(80);

        $form->addText('prefixTitle', 'Titul před')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teacher ? $teacher['titul_pred'] : '')
            ->setMaxLength(40);

        $form->addText('postfixTitle', 'Titul za')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teacher ? $teacher['titul_za'] : '')
            ->setMaxLength(40);

        $form->addText('telephone', 'Telefon')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setHtmlAttribute('placeholder', '+420777666555')
            ->setDefaultValue($teacher ? $teacher['telefon'] : '')
            ->setMaxLength(13);

        $form->addText('mobile', 'Mobil')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setHtmlAttribute('placeholder', '+420777666555')
            ->setDefaultValue($teacher ? $teacher['mobil'] : '')
            ->setMaxLength(13);

        $form->addText('email', 'Kontaktní e-mail')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($teacher ? $teacher['kontaktni_email'] : '')
            ->setMaxLength(255);

        $form->addSelect('department', 'Katedra', array_reduce($departments, function ($result, $department) {
            $result[$department['id']] = $department['zkratka'] . ' - ' . $department['nazev'];
            return $result;
        }))
            ->setRequired("Prosím vyplňte katedru");;

        $form->addSubmit('send', $teacher ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->teachers = $this->teacherModel->getAll();
        $this->template->tabs = [];
    }

    public function renderEdit(string $id): void {
        if(!$this->isOwner()) {
            $this->requireAdmin();
        }

        $this->template->isOwner = $this->isOwner();
        $this->template->tabs = [
            'Učitelé' => 'Teacher:',
            'Můj účet' => ['User:edit', $this->getUser()->getId()]
        ];
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add teacher form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if(empty($this->getParameter('id'))) {
                $this->teacherModel->insert($form->getValues(true));
                $this->flashMessage('Vyučující byl přidán.', self::$SUCCESS);
            } else {
                $this->teacherModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Vyučující byl upraven.', self::$SUCCESS);
            }

            $this->redirect('Teacher:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

    }

    /**
     * Delete faculty by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->teacherModel->deleteById($id);
            $this->flashMessage('Vyučující byl vymazán.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Teacher:');
    }

    /**
     * ID of edited teacher and logged user are same.
     * @return bool User and teacher are same.
     */
    private function isOwner(): bool {
        $id = $this->getParameter('id');
        return isset($id) && $this->getUser()->getIdentity()->teacherId === $id;
    }


}