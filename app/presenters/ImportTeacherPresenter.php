<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-01
 * Time: 21:16
 */

namespace App\Presenters;


use App\Model\ImportModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class ImportTeacherPresenter extends BasePresenter {

    private $importModel;

    /**
     * ImportPresenter constructor.
     * @param $importModel
     */
    public function __construct(ImportModel $importModel) {
        parent::__construct();
        $this->importModel = $importModel;
    }

    /**
     * Create import form.
     * @return Form
     */
    function createComponentImportTeachersForm() {
        $form = new Form;
        $form->addText('name', 'Jméno')
            ->setRequired('Prosím vyplňte název pro jméno.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('jmeno')
            ->setMaxLength(50);

        $form->addText('surname', 'Příjmení')
            ->setRequired('Prosím vyplňte název pro příjmení.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('prijmeni')
            ->setMaxLength(50);

        $form->addText('titlePrefix', 'Titul před')
            ->setRequired('Prosím vyplňte název pro titul před.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('titulPred')
            ->setMaxLength(50);

        $form->addText('titlePostfix', 'Titul za')
            ->setRequired('Prosím vyplňte název pro titul za.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('titulZa')
            ->setMaxLength(50);

        $form->addText('telephone', 'telefon')
            ->setRequired('Prosím vyplňte název pro telefon.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('telefon')
            ->setMaxLength(50);

        $form->addText('mobile', 'Mobil')
            ->setRequired('Prosím vyplňte název pro mobil.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('telefon2')
            ->setMaxLength(50);

        $form->addText('email', 'Kontaktní email')
            ->setRequired('Prosím vyplňte název pro kontaktní email.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue('email')
            ->setMaxLength(50);

        $form->addUpload('file', 'Importovaný soubor (JSON)')
            ->setRequired(true);


        $form->addSubmit('send', 'Importovat');

        $form->onSuccess[] = [$this, 'onImport'];
        return $form;
    }

    function renderDefault(){
        $this->requireAdmin();
        $this->template->teachers = [];
    }

    /**
     * Handler for import form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onImport(Form $form): void {
        $this->requireAdmin();
        try {
            /** @var \Nette\Http\FileUpload */
            $fileUpload = $form['file']->getValue();
            $fileData = $fileUpload->getContents();
            $json = json_decode($fileData, true);
            if ($json === null || $json[0]['ucitel'] === null) {
                $this->flashMessage("Nevalidní JSON!", self::$ERROR);
                return;
            }

            $teachers = $json[0]['ucitel'];
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

}