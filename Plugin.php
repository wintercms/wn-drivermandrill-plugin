<?php namespace Winter\Mandrill;

use App;
use Backend;
use Event;
use Backend\Models\UserRole;
use System\Classes\PluginBase;
use System\Models\MailSetting;

/**
 * Mailgun Plugin Information File
 */
class Plugin extends PluginBase
{
    const MODE_MANDRILL = 'mandrill';

    public function pluginDetails()
    {
        return [
            'name'        => 'Mandrill',
            'description' => 'winter.mandrill::lang.plugin_description',
            'author'      => 'Winter',
            'icon'        => 'icon-leaf'
        ];
    }

    public function register()
    {
        Event::listen('mailer.beforeRegister', function () {
            $settings = MailSetting::instance();
            if ($settings->send_mode === self::MODE_MANDRILL) {
                $config = App::make('config');
                $config->set('services.mandrill.secret', $settings->mandrill_secret);
            }
        });

    }

    public function boot()
    {
        MailSetting::extend(function ($model) {
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                $model->rules['mandrill_secret'] = 'required_if:send_mode,' . self::MODE_MANDRILL;
            });
        });

        Event::listen('backend.form.extendFields', function ($widget) {
            if (!$widget->getController() instanceof \System\Controllers\Settings) {
                return;
            }
            if (!$widget->model instanceof MailSetting) {
                return;
            }

            $field = $widget->getField('send_mode');
            $field->options(array_merge($field->options(), [self::MODE_MANDRILL => "Mandrill"]));

            $widget->addTabFields([
                'mandrill_secret' => [
                    "tab"     => "system::lang.mail.general",
                    'label'   => 'winter.mandrill::lang.fields.mandrill_secret.label',
                    'comment' => 'winter.mandrill::lang.fields.mandrill_secret.comment',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mandrill]'
                    ]
                ],
            ]);
        });
    }
}
