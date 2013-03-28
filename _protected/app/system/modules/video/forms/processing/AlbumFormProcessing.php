<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Image\Image,
PH7\Framework\Util\Various,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Url\HeaderUrl;

class AlbumFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        /**
         * This can cause minor errors (eg if a user sent a file that is not a video).
         * So we hide the errors if we are not in development mode.
         */
        if(!isDebug()) error_reporting(0);

        // Resizing and saving the video album thumbnail
        $oPicture = new Image($_FILES['album']['tmp_name']);
        if(!$oPicture->validate())
        {
            \PFBC\Form::setError('form_video_album', Form::wrongImgFileTypeMsg());
        }
        else
        {
            $iApproved = (DbConfig::getSetting('videoManualApproval') == 0) ? '1' : '0';

            $sFileName = Various::genRnd($oPicture->getFileName(), 1) . '-thumb.' . $oPicture->getExt();

            (new VideoModel)->addAlbum($this->session->get('member_id'), $this->httpRequest->post('name'), $this->httpRequest->post('description'), $sFileName, $this->dateTime->get()->dateTime('Y-m-d H:i:s'), $iApproved);
            $iLastAlbumId = (int) Db::getInstance()->lastInsertId();

            $oPicture->square(200);

            /* Set watermark text on thumbnail */
            $sWatermarkText = DbConfig::getSetting('watermarkTextImage');
            $iSizeWatermarkText = DbConfig::getSetting('sizeWatermarkTextImage');
            $oPicture->watermarkText($sWatermarkText, $iSizeWatermarkText);

            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'video/file/' . $this->session->get('member_username') . PH7_DS . $iLastAlbumId . PH7_DS;

            $this->file->createDir($sPath);

            $oPicture->save($sPath . $sFileName);

            /* Clean VideoModel Cache */
            (new Framework\Cache\Cache)->start(VideoModel::CACHE_GROUP, null, null)->clear();

            HeaderUrl::redirect(UriRoute::get('video', 'main', 'addvideo', $iLastAlbumId));
        }
    }

}
