<?php namespace Winter\DriverMandrill;

use App;
use Event;
use System\Classes\PluginBase;
use System\Models\MailSetting;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillTransportFactory;

/**
 * DriverMandrill Plugin Information File
 */
class Plugin extends PluginBase
{
    const MODE_MANDRILL = 'mandrill';

    public function pluginDetails()
    {
        return [
            'name'        => 'winter.drivermandrill::lang.plugin.name',
            'description' => 'winter.drivermandrill::lang.plugin.description',
            'homepage'    => 'https://github.com/wintercms/wn-drivermandrill-plugin',
            'author'      => 'Winter',
            'icon'        => 'icon-leaf',
        ];
    }

    public function register()
    {
        Event::listen('mailer.beforeRegister', function ($mailManager) {
            $mailManager->extend(self::MODE_MANDRILL, function ($config) {
                $factory = new MandrillTransportFactory();

                if (!isset($config['secret'])) {
                    $config = $this->app['config']->get('services.mandrill', []);
                }

                return $factory->create(new Dsn(
                    'mandrill+'.($config['scheme'] ?? 'api'),
                    $config['endpoint'] ?? 'default',
                    $config['secret']
                ));
            });

            $settings = MailSetting::instance();
            if ($settings->send_mode === self::MODE_MANDRILL) {
                $config = App::make('config');
                $config->set('mail.mailers.mandrill.transport', self::MODE_MANDRILL);
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
            $model->mandrill_secret = config('services.mandrill.secret');
        });

        Event::listen('backend.form.extendFields', function ($widget) {
            if (!$widget->getController() instanceof \System\Controllers\Settings) {
                return;
            }
            if (!$widget->model instanceof MailSetting) {
                return;
            }

            $field = $widget->getField('send_mode');
            $field->options(array_merge($field->options(), [self::MODE_MANDRILL => 'Mandrill']));

            $widget->addTabFields([
                'mandrill_secret' => [
                    'tab'     => 'system::lang.mail.general',
                    'label'   => 'winter.drivermandrill::lang.mandrill_secret',
                    'commentAbove' => 'winter.drivermandrill::lang.mandrill_secret_comment',
                    'type'    => 'sensitive',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mandrill]',
                    ],
                ],
            ]);
        });
    }
}
