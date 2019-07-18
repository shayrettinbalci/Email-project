<?php

namespace frontend\components;


use Cloudinary;
use \yii\base\Component;

class CloudinaryComponent extends Component {

    public $cloud_name;
    public $api_key;
    public $api_secret;

    public function init() {

        parent::init();

        Cloudinary::config(array(
            'cloud_name' => $this->cloud_name,
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret
        ));

    }
}