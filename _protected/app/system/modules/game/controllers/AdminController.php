<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Controller
 */
namespace PH7;
use PH7\Framework\Mvc\Router\UriRoute, PH7\Framework\Url\HeaderUrl;

class AdminController extends MainController
{

    public function index()
    {
        HeaderUrl::redirect(UriRoute::get('game', 'main', 'index'), t('Welcome to the Game administrator mode.'));
    }

    public function add()
    {
        $this->sTitle = t('Add a new Game');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit Game');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function delete()
    {
        if( $this->httpRequest->postExists( array('id', 'thumb', 'file') ))
        {
            $this->oGameModel->delete( $this->httpRequest->post('id') );

            $aFiles = [
                'thumb' => PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/img/thumb/' . $this->httpRequest->post('thumb'),
                'file' => PH7_PATH_PUBLIC_DATA_SYS_MOD . 'game/file/' . $this->httpRequest->post('file')
            ];

            $this->file->deleteFile($aFiles);

            /* Clean GameModel Cache */
            (new Framework\Cache\Cache)->start(GameModel::CACHE_GROUP, null, null)->clear();

            $sMsg = t('The game has been removed.');
        }
        else
        {
            $sMsg = t('The game could not be removed.');
        }

        HeaderUrl::redirect(UriRoute::get('game', 'admin', 'index'), $sMsg);
    }

}
