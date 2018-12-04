<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-03
 * Time: 19:39
 */

namespace App\Presenters;


use App\Model\ObligationModel;

class ObligationPresenter extends BasePresenter {

    private $obligationModel;

    /**
     * ObligationPresenter constructor.
     * @param $obligationModel
     */
    public function __construct(ObligationModel $obligationModel) {
        $this->obligationModel = $obligationModel;
    }


    function renderDefault() {
        if (!$this->user->isInRole('teacher')) {
            $this->flashMessage('Nedostatečná oprávnění');
            $this->redirect('Homepage:');
        }

        $this->template->obligations = $this->obligationModel->getMyObligations($this->user->getIdentity()->teacherId);

        $sum = 0;
        foreach ($this->template->obligations as $obligation) {
            $sum += $obligation->hodiny;
        }
        $this->template->hours = $sum;

        $this->template->tabs = [
            'Můj profil učitele' => ['Teacher:edit', $this->getUser()->getIdentity()->teacherId],
            'Můj účet' => ['User:edit', $this->getUser()->id],
            'Odhlásit' => 'Sign:out'
        ];
    }
}