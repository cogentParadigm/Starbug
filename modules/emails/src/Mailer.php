<?php
namespace Starbug\Emails;

use PHPMailer\PHPMailer\PHPMailer;
use Starbug\Core\MacroInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Settings\SettingsInterface;

class Mailer implements MailerInterface {

  protected $host;
  protected $username;
  protected $password;
  protected $from_email;
  protected $from_name;
  protected $port;
  protected $secure;

  public function __construct(
    protected SettingsInterface $settings,
    protected MacroInterface $macro,
    protected DatabaseInterface $db,
    protected $whitelistEnabled = false,
    protected $whitelist = []
  ) {
    $this->host = $settings->get("email_host");
    $this->username = $settings->get("email_username");
    $this->password = $settings->get("email_password");
    $this->from_email = $settings->get("email_address");
    $this->from_name = $settings->get("site_name");
    $this->port = $settings->get("email_port");
    $this->secure = $settings->get("email_secure");
  }

  public function create() {
    $mailer = new PHPMailer(true);
    if ($this->host) {
      $mailer->IsSMTP(); // send via SMTP
      $mailer->Host     = $this->host;
      if (!empty($this->username)) {
        $mailer->SMTPAuth = true;  // turn on SMTP authentication
        $mailer->Username = $this->username;    // SMTP username
        $mailer->Password = $this->password;    // SMTP password
      }
    }
    if ($this->from_email) {
      $mailer->From = $this->from_email;
    }
    if ($this->from_name) {
      $mailer->FromName = $this->from_name;
    }
    if (!empty($this->port)) {
      $mailer->Port = $this->port;
    }
    if (!empty($this->secure)) {
      $mailer->SMTPSecure = $this->secure;
    }
    $mailer->WordWrap = 50;
    $mailer->IsHTML(true);
    return $mailer;
  }

  public function render($options = [], $data = []) {
    $data['absolute_urls'] = true;
    // get template params
    if (!empty($options['template'])) {
      $template = $this->db->query("email_templates")->condition("name", $options['template'])->one();
      if (!empty($template)) {
        $options = array_merge($template, $options);
      }
    }
    // set mailer params
    $arr = ["to", "cc", "bcc"];
    $replace = ["from", "from_name", "subject", "body", "to", "cc", "bcc"];
    foreach ($replace as $key) {
      if (!empty($options[$key])) {
        if (in_array($key, $arr) && !is_array($options[$key])) {
          $options[$key] = explode(",", $options[$key]);
        }
        if (is_array($options[$key])) {
          foreach ($options[$key] as $idx => $value) {
            $options[$key][$idx] = $this->macro->replace(trim($value), $data);
          }
        } else {
          $options[$key] = $this->macro->replace($options[$key], $data);
        }
      }
    }
    return $options;
  }

  /**
   * Send an email email.
   *
   * @param array $options
   * @param array $data
   */
  public function send($options = [], $data = [], $rendered = false) {
    $mailer = $this->create();
    if (!$rendered) {
      $options = $this->render($options, $data);
    }

    if ($this->whitelistEnabled) {
      $validated = [];
      foreach ($options["to"] as $email) {
        foreach ($this->whitelist as $pattern) {
          if (preg_match($pattern, $email)) {
            $validated[] = $email;
            break;
          };
        }
      }
      if (empty($validated)) {
        return 1;
      }
      $options["to"] = $validated;
    }

    // set mailer params
    if (!empty($options['from'])) {
      $mailer->From = $options['from'];
    }
    if (!empty($options['from_name'])) {
      $mailer->FromName = $options['from_name'];
    }
    if (!empty($options['subject'])) {
      $mailer->Subject = $options['subject'];
    }
    if (!empty($options['body'])) {
      $mailer->Body = $options['body'];
    }
    if (!empty($options['to'])) {
      foreach ($options['to'] as $email) {
        $mailer->AddAddress($email);
      }
    }
    if (!empty($options['cc'])) {
      foreach ($options['cc'] as $cc) {
        $mailer->AddCC($cc);
      }
    }
    if (!empty($options['bcc'])) {
      foreach ($options['bcc'] as $bcc) {
        $mailer->AddBCC($bcc);
      }
    }
    if (!empty($options['attachments'])) {
      $attachments = $options['attachments'];
      foreach ($attachments as $a) {
        if (is_array($a)) {
          $mailer->AddAttachment($a[0], $a[1]);
        } else {
          $mailer->AddAttachment($a);
        }
      }
    }
    // send mail
    $result = $mailer->Send();
    return $result;
  }
}
