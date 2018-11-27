<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:32
 */

namespace App\Presenters;


use App\Model\RoomModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class RoomPresenter extends BasePresenter {

    private $roomModel;

    /**
     * RoomPresenter constructor.
     * @param $roomModel
     */
    public function __construct(RoomModel $roomModel) {
        parent::__construct();
        $this->roomModel = $roomModel;
    }

    /**
     * Create edit room form.
     * @return Form Edit room form
     */
    protected function createComponentEditRoomForm(): Form {
        $roomId = $this->getParameter('id');
        $room = isset($roomId) ? $this->roomModel->getById($roomId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($room ? $room['nazev'] : '')
            ->setMaxLength(50);

        $form->addInteger('capacity', 'Kapacita')
            ->setRequired('Prosím vyplňte kapacitu.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($room ? $room['kapacita'] : '');

        $form->addSubmit('send', $room ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->rooms = $this->roomModel->getAll();
        $this->template->tabs = ['Katedry' => 'Department:', 'Fakulty' => 'Faculty:'];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add room form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if (empty($this->getParameter('id'))) {
                $this->roomModel->insert($form->getValues(true));
                $this->flashMessage('Místnost byla přidána.', self::$SUCCESS);
            } else {
                $this->roomModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Místnost byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Room:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete room by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->roomModel->deleteById($id);
            $this->flashMessage('Místnost byla vymazána.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Room:');
    }

}