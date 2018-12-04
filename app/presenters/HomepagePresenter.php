<?php

namespace App\Presenters;

final class HomepagePresenter extends BasePresenter {

    public function startup() {
        parent::startup();
        $this->redirect('Schedule:');
    }

}
