<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-03
 * Time: 19:39
 */

namespace App\Presenters;


use App\Model\ObligationModel;
use App\Model\TeacherModel;
use Nette\Application\UI\Form;

class ObligationPresenter extends BasePresenter {

    private $obligationModel;

    private $teacherModel;

    /**
     * ObligationPresenter constructor.
     * @param ObligationModel $obligationModel
     * @param TeacherModel $teacherModel
     */
    public function __construct(ObligationModel $obligationModel, TeacherModel $teacherModel) {
        parent::__construct();
        $this->obligationModel = $obligationModel;
        $this->teacherModel = $teacherModel;
    }

    public function createComponentFilterObligationForm(): Form {
        $teachers = $this->teacherModel->getAll();
        $form = new Form;

        $form->addSelect('teacher', null, array_reduce($teachers, function ($result, $teacher) {
            $result[$teacher['id']] = $teacher['jmeno'] . ' ' . $teacher['prijmeni'];
            return $result;
        }))
            ->setDefaultValue($this->getHttpRequest()->getQuery('teacherId'));

        $form->addSubmit('send', 'Vyhledat');

        $form->onSuccess[] = [$this, 'onFilter'];

        return $form;
    }

    function renderDefault() {
        $this->requireAdmin();

        $id = $this->getHttpRequest()->getQuery('teacherId');
        if (empty($id)) {
            $teachers = $this->teacherModel->getAll();
            if (empty($teachers)) {
                $this->flashMessage('Neexistují žádní učitelé', self::$ERROR);
                $this->redirect('Teacher:');
            } else {
                $id = $teachers[0]->id;
            }
        }

        $this->handleObligationsById($id, false);
    }

    function renderMy() {
        if (!$this->user->isInRole('teacher')) {
            $this->flashMessage('Nedostatečná oprávnění', self::$ERROR);
            $this->redirect('Homepage:');
        }

        $this->handleObligationsById($this->user->getIdentity()->teacherId, true);
    }

    private function handleObligationsById($id, $isMyPage) {
        $this->template->obligations = $this->obligationModel->getMyObligations($id);

        $sum = 0;
        foreach ($this->template->obligations as $obligation) {
            $sum += $obligation->hodiny;
        }
        $this->template->hours = $sum;

        if ($isMyPage) {
            $this->template->tabs = [
                'Můj profil učitele' => ['Teacher:edit', $this->getUser()->getIdentity()->teacherId],
                'Můj účet' => ['User:edit', $this->getUser()->id],
                'Odhlásit' => 'Sign:out'
            ];
        } else {
            $this->template->tabs = [
                'Učitelé' => 'Teacher:',
                'Role' => 'Role:'
            ];
        }
    }

    public function onFilter(Form $form, array $values): void {
        $this->redirect('Obligation:', [
            'teacherId' => $values['teacher']
        ]);
    }
}