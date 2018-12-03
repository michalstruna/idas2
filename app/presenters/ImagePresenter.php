<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-03
 * Time: 13:17
 */

namespace App\Presenters;


use App\Model\ImageModel;
use Nette\Utils\Image;

class ImagePresenter extends BasePresenter {

    private $imageModel;

    /**
     * ImagePresenter constructor.
     * @param $imageModel
     */
    public function __construct(ImageModel $imageModel) {
        parent::__construct();
        $this->imageModel = $imageModel;
    }


    public function actionLoad() {
        $id = $this->getParameter('id');
        if ($id === null) {
            $this->showDefault();
            return;
        }

        $imageData = $this->imageModel->getById($id);
        if ($imageData === false) {
            $this->showDefault();
            return;
        }

        header('Content-Type: '. $imageData['TYP']);
        print $imageData['OBRAZEK']->load();
    }

    private function showDefault() {
        dump(Image::fromFile('./../../img/blank.png'));
        die();
        $img = './../../www/img/blank.png';
        header('Content-Type: image/png');
        readfile($img);
    }
}