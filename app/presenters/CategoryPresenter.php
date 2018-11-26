<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:21
 */

namespace App\Presenters;


use App\Model\CategoryModel;
use Nette\Database\DriverException;
use Nette\Application\UI\Form;

class CategoryPresenter extends BasePresenter {

    private $categoryModel;

    /**
     * CategoryPresenter constructor.
     * @param $categoryModel
     */
    public function __construct(CategoryModel $categoryModel) {
        parent::__construct();
        $this->categoryModel = $categoryModel;
    }

    /**
     * Create edit category form.
     * @return Form Edit category form
     */
    protected function createComponentEditCategoryForm(): Form {
        $categoryId = $this->getParameter('id');
        $category = isset($categoryId) ? $this->categoryModel->getById($categoryId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($category ? $category['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $category ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->categories = $this->categoryModel->getAll();
        $this->template->tabs = [
            'Způsoby zakončení' => 'CompletionType:',
            'Formy výuky' => 'TeachingForm:',
            'Způsoby výuky' => 'CourseType:',
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
     * Handler for edit or add category form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->categoryModel->insert($form->getValues(true));
                $this->flashMessage('Kategorie byla přidána.', self::$SUCCESS);
            } else {
                $this->categoryModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Kategorie byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Category:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete category by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->categoryModel->deleteById($id);
            $this->flashMessage('Kategorie byla vymazána.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Category:');
    }

}