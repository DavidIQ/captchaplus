<?php
/**
 *
 * CAPTCHA+. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, David Colón, https://www.davidiq.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace davidiq\captchaplus;

/**
 * CAPTCHA+ Service info.
 */
class service
{
    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var /phpbb/config/config */
    protected $config;

    /** @var \phpbb\captcha\factory */
    protected $captcha_factory;

    /**
     * Constructor
     *
     * @param \phpbb\user $user User object
     * @param \phpbb\auth\auth $auth Auth object
     * @param \phpbb\config\config $config Configuration object
     * @param \phpbb\captcha\factory $captcha_factory CAPTCHA factory object
     */
    public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\captcha\factory $captcha_factory)
    {
        $this->user = $user;
        $this->auth = $auth;
        $this->config = $config;
        $this->captcha_factory = $captcha_factory;
    }

    /**
     * Initializes CAPTCHA for use when needed
     *
     * @param integer $forum_id The forum ID to use for auth check
     * @return \phpbb\captcha\plugins\captcha_abstract|object|null
     */
    public function init(int $forum_id)
    {
        $can_post_without_captcha = $this->auth->acl_get('f_nopostcaptcha', $forum_id);
        if (!$can_post_without_captcha && !empty($this->config['captcha_plugin']))
        {
            $captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
            $captcha->init(CONFIRM_POST);
            return $captcha;
        }
        return null;
    }

    /**
     * Validates the CAPTCHA
     *
     * @param \phpbb\captcha\plugins\captcha_abstract|null $captcha CAPTCHA plugin object
     * @param string $message Message used for CAPTCHA data validation
     * @param string $subject Subject used for CAPTCHA data validation
     * @param string $username Username used for CAPTCHA data validation
     * @return mixed|null Error response for CAPTCHA
     */
    public function validate($captcha, string $message, string $subject, string $username)
    {
        if (!isset($captcha))
        {
            return null;
        }

        $captcha_data = [
            'message' => $message,
            'subject' => $subject,
            'username' => $username,
        ];
        $vc_response = $captcha->validate($captcha_data);
        if ($vc_response)
        {
            return $vc_response;
        }
        return null;
    }

    /**
     * Checks if the CAPTCHA needs a reset after submit
     *
     * @param \phpbb\captcha\plugins\captcha_abstract|null $captcha CAPTCHA plugin object
     */
    public function reset($captcha)
    {
        if (!isset($captcha))
        {
            return;
        }

        if ($captcha->is_solved() === true)
        {
            $captcha->reset();
        }
    }

    /**
     * Add the CAPTCHA template
     *
     * @param \phpbb\captcha\plugins\captcha_abstract|null $captcha CAPTCHA plugin object
     * @param string $s_hidden_fields String with hidden fields
     * @param array $template_data Template data
     * @return bool
     */
    public function add_captcha_template($captcha, string &$s_hidden_fields, array &$template_data)
    {
        if (!isset($captcha))
        {
            return false;
        }

        if ($captcha->is_solved() === false)
        {
            $s_hidden_fields .= build_hidden_fields($captcha->get_hidden_fields());
            $template_data['CAPTCHAPLUS_TEMPLATE'] = $captcha->get_template();
            return true;
        }
        return false;
    }
}
