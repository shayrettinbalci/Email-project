<?php /** @noinspection PhpUndefinedFieldInspection */

namespace frontend\controllers;


use Cloudinary\Uploader;
use frontend\models\Attachment;
use frontend\models\EmailForm;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class EmailController extends Controller
{
    /**
     * Cannot send email without login
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['attachment', 'create', 'update', 'upload'],
                'rules' => [
                    [
                        'actions' => ['attachment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['upload'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'update' => ['get', 'post'],
                ],
            ],
        ];

    }

    /**
     * Stop Csrf validation while upload to cloudinary
     * @inheritdoc
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id == 'upload') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Mail creation.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new EmailForm();

        if(Yii::$app->request->post()){
            if($model->load(Yii::$app->request->post()) && $model->validate())
            {
                if($model->mailSave()){
                    Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
                    return $this->refresh();}
                else {
                    Yii::$app->session->setFlash('error', 'There was an error sending your message.');
                }
            }
            else {
                return $this->render('email/_create', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('_create', [
            'model' => $model,
        ]);
    }

    /**
     * If delete attachments by mistake, they comeback when refresh page
     * Mail update
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->post()) {
            if($model->load(Yii::$app->request->post()) && $model->validate()) {
                if($model->mailSave() && $model->deleteAttachments()){
                    Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');

                    return $this->redirect(['site/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'There was an error sending your message.');
                }
            }
        }

        $refreshAttachments =  Attachment::findAll(['email_id' => $id, 'is_deleted' => true]);
        if(isset($refreshAttachments)){
            foreach($refreshAttachments as $refreshAttachment){
                $refreshAttachment->is_deleted = 0;
                $refreshAttachment->save();
            }
        }

        return $this->render('_update', [
            'model' => $model,
            'attachmentInitialPreviews' => ArrayHelper::getColumn($model->attachments, 'downloadUrl'),
            'attachmentInitialPreviewConfigs' => $model->attachments
        ]);
    }

    /**
     * Mail sending
     * Response JSON
     * @return string
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionSendEmail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new EmailForm();

        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(!empty($emailId = Yii::$app->request->post("emailId"))) {
                $this->findModel($emailId);
                if($model->sendEmail($emailId))
                {
                    return 'success';
                }
            }
            if($model->sendEmail()){
                return 'success';}
            else {
                return 'error';
            }
        } else {

            return 'error';
        }
    }

    /**
     * Upload photos to cloudinary.com
     * @return array
     */
    public function actionUpload()
    {
        if (isset(Yii::$app)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->cloudinary;
        }
        $cloudinary_preset = ''; // add new upload preset from cloudinary.com/console/settings/upload

        $image = UploadedFile::getInstanceByName("upload");
        $response = Uploader::unsigned_upload($image->tempName, $cloudinary_preset);

        return [
            'fileName' => $image->name,
            'uploaded' => true,
            'url' => $response["secure_url"]
        ];
    }

    /**
     * If click the delete button in attachments, it makes true is_deleted.
     * respense JSON
     * @return string
     */
    public function actionFileDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($key = Yii::$app->request->post())
        {
            $file = Attachment::findOne($key);
            $file->is_deleted = 1;
            $file->save();
        }
        return "success";
    }

    /**
     * Finds the Email model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmailForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }




}

