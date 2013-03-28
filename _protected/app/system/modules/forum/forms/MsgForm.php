<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */
namespace PH7;

use
PH7\Framework\Config\Config,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Request\HttpRequest;

class MsgForm
{

    public static function display()
    {
        if (isset($_POST['submit_msg']))
        {
            if (\PFBC\Form::isValid($_POST['submit_msg']))
                new MsgFormProcessing();

            Framework\Url\HeaderUrl::redirect();
        }

        $iForumsId = (new ForumModel)->getForum(null, 0, 300);

        $aForumsName = array();
        foreach ($iForumsId as $id)
            $aForumsName[$id->forumId] = $id->name;

        $oForm = new \PFBC\Form('form_msg', '100%');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_msg', 'form_msg'));
        $oForm->addElement(new \PFBC\Element\Token('msg'));

        $oHttpRequest = new HttpRequest;
        $oForm->addElement(new \PFBC\Element\Select(t('Forum:'), 'forum', $aForumsName, array('value'=>$oHttpRequest->get('forum_id'))));
        unset($aForumsName, $oHttpRequest);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Subject:'), 'title', array('id'=>'str_title', 'onblur'=>'CValid(this.value,this.id,4,60)', 'required' => 1, 'validation'=>new \PFBC\Validation\RegExp(Config::getInstance()->values['module.setting']['url_title.pattern']))));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error str_title"></span>'));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Message:'), 'message', array('required' => 1, 'validation'=>new \PFBC\Validation\Str(4))));
        if (DbConfig::getSetting('isCaptchaForum'))
        {
            $oForm->addElement(new \PFBC\Element\CCaptcha(t('Captcha:'), 'captcha', array('id'=>'ccaptcha','onkeyup'=>'CValid(this.value, this.id)','description'=>t('Enter the code above:'))));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'validate.js"></script>'));
        $oForm->render();
    }

}
