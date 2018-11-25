<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 20:21
 */

namespace App\Presenters;


use App\Model\SubjectModel;

final class SubjectPresenter extends BasePresenter {

    private $subjectModel;

    /**
     * SubjectPresenter constructor.
     * @param $subjectModel
     */
    public function __construct(SubjectModel $subjectModel)
    {
        parent::__construct();
        $this->subjectModel = $subjectModel;
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
}