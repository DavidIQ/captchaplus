<?php
/**
 *
 * CAPTCHA 4 post. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\captcha4post\event;

/**
 * @ignore
 */

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * CAPTCHA 4 post Event listener.
 */
class main_listener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'core.permissions' => 'add_permissions',
            'core.modify_posting_auth' => 'init_captcha4post',
            'core.posting_modify_message_text' => 'validate_captcha4post',
            'core.posting_modify_submit_post_after' => 'after_submit_check',
            'core.posting_modify_template_vars' => 'add_captcha4post',
        ];
    }

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var /phpbb/config/config */
    protected $config;

    /** @var \phpbb\captcha\factory */
    protected $captcha_factory;

    /** @var \phpbb\captcha\plugins\captcha_abstract */
    private $captcha;

    /**
     * Constructor
     *
     * @param \phpbb\user $user User object
     */
    public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\captcha\factory $captcha_factory)
    {
        $this->user = $user;
        $this->auth = $auth;
        $this->config = $config;
        $this->captcha_factory = $captcha_factory;
    }

    /**
     * Add permissions to the ACP -> Permissions settings page
     *
     * @param \phpbb\event\data $event Event object
     */
    public function add_permissions($event)
    {
        $permissions = $event['permissions'];
        $permissions['f_nocaptcha4post'] = ['lang' => 'ACL_F_NOCAPTCHA4POST', 'cat' => 'post'];
        $event['permissions'] = $permissions;
    }

    /**
     * Adds CAPTCHA to posting page when needed
     *
     * @param \phpbb\event\data $event Event object
     */
    public function init_captcha4post($event)
    {
        $forum_id = (int)$event['forum_id'];
        $can_post_without_captcha = $this->auth->acl_get('f_nocaptcha4post', $forum_id);
        if (!$can_post_without_captcha && !empty($this->config['captcha_plugin']))
        {
            $this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
            $this->captcha->init(CONFIRM_POST);
        }
    }

    /**
     * Validates the CAPTCHA
     *
     * @param \phpbb\event\data $event Event object
     */
    public function validate_captcha4post($event)
    {
        if (!isset($this->captcha))
        {
            return;
        }

        $message_parser = $event['message_parser'];
        $message = $message_parser->message;

        $post_data = $event['post_data'];
        $subject = $post_data['post_subject'];
        $username = $post_data['username'];

        $captcha_data = array(
            'message' => $message,
            'subject' => $subject,
            'username' => $username,
        );
        $vc_response = $this->captcha->validate($captcha_data);
        if ($vc_response)
        {
            $error = $event['error'];
            $error[] = $vc_response;
            $event['error'] = $error;
        }
    }

    /**
     * Checks if the CAPTCHA needs a reset after submit
     *
     * @param \phpbb\event\data $event Event object
     */
    public function after_submit_check($event)
    {
        if (!isset($this->captcha))
        {
            return;
        }

        if ($this->captcha->is_solved() === true)
        {
            $this->captcha->reset();
        }
    }

    /**
     * Add the CAPTCHA template
     *
     * @param \phpbb\event\data $event Event object
     */
    public function add_captcha4post($event)
    {
        if (!isset($this->captcha))
        {
            return;
        }

        if ($this->captcha->is_solved() === false)
        {
            $s_hidden_fields = $event['s_hidden_fields'];
            $s_hidden_fields .= build_hidden_fields($this->captcha->get_hidden_fields());
            $event['s_hidden_fields'] = $s_hidden_fields;

            $page_data = $event['page_data'];
            $page_data['CAPTCHA4POST_TEMPLATE'] = $this->captcha->get_template();
            $event['page_data'] = $page_data;
        }
    }
}
